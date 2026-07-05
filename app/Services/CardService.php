<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Jobs\CardActivate;
use App\Models\Card;
use App\Models\Transaction;
use App\Traits\VirtualCard;
use Card\Stripe\StripeCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CardService
{
    use VirtualCard;

    public function validate($request)
    {
        if (! setting('card_creation', 'permission')) {
            throw ValidationException::withMessages(['error' => __('Card creation is unavailable.')]);
        }

        $cardCreationLimit = setting('card_creation_limit', 'virtual_card');
        $totalCreatedCards = Card::currentUser()->count();

        if ($totalCreatedCards >= $cardCreationLimit) {
            throw ValidationException::withMessages(['error' => __('Card creation limit reached.')]);
        }
    }

    public function process($request)
    {
        $user = $request->user();
        $provider = $this->cardProviderMap($this->cardProviderCode($request->card_provider_name));

        $validator_rules = $provider->validationRules($request);

        // Validate request data
        $validator = Validator::make($request->all(), $validator_rules);
        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }

        try {
            // Start transaction
            DB::beginTransaction();

            // Check if user has sufficient balance
            $charge = setting('card_creation_charge', 'virtual_card');
            if ($user->balance < $charge) {
                throw ValidationException::withMessages(['error' => __('Insufficient balance!')]);
            }

            // Creating a card holder
            $card_holder = $provider->getCardHolder($request);

            // Creating a card
            if ($provider instanceof StripeCard) {
                $cardResponse = $provider->execute($card_holder);
            } else {
                $cardResponse = $provider->execute();
            }

            // Create card in database
            $card = Card::create([
                'user_id' => $user->id,
                'card_holder_id' => $card_holder->id,
                'provider' => 'stripe',
                'card_id' => $cardResponse['card']->id,
                'currency' => 'usd',
                'type' => 'virtual',
                'status' => $cardResponse['data']['status'],
                'amount' => $cardResponse['data']['amount'],
                'card_number' => data_get($cardResponse['card'], 'card_number', '000000000000'.$cardResponse['card']?->last4),
                'cvc' => data_get($cardResponse['card'], 'cvc', '123'),
                'expiration_month' => $cardResponse['card']?->exp_month,
                'expiration_year' => $cardResponse['card']?->exp_year,
                'last_four_digits' => $cardResponse['card']?->last4,
            ]);

            dispatch(new CardActivate($cardResponse['card']->id));

            // Deduct amount from user wallet and create transaction for card creation
            $user->decrement('balance', $charge);

            // fetch card creation charge
            $charge = setting('card_creation_charge', 'virtual_card');

            // create transaction
            Transaction::create([
                'user_id' => $user->id,
                'description' => 'Card Creation Charge',
                'type' => TxnType::CardCreate,
                'amount' => $charge,
                'charge' => 0,
                'final_amount' => $charge,
                'wallet_type' => 'default',
                'method' => 'System',
                'status' => TxnStatus::Success,
            ]);

            // Commit transaction
            DB::commit();

            return $card;
        } catch (\Throwable $th) {
            // Rollback transaction
            DB::rollBack();

            throw $th;
        }
    }

    public function transactions($card_id)
    {
        $card = Card::with('cardHolder')->currentUser()->where('card_id', $card_id)->firstOrFail();

        // Provider instance
        $provider = $this->cardProviderMap($card->provider);

        // Load card transactions
        $provider_transaction = $provider->getCardTransactions($card->card_id);

        return $provider_transaction;
    }

    protected function validateTopup($request)
    {
        // Validate amount
        $min_topup = setting('min_card_topup', 'virtual_card');
        $max_topup = setting('max_card_topup', 'virtual_card');
        $amount = $request->get('amount');

        if ($amount < $min_topup || $amount > $max_topup) {
            $currencySymbol = setting('currency_symbol', 'global');
            $message = 'Please topup the amount within the range '.$currencySymbol.$min_topup.' to '.$currencySymbol.$max_topup;
            throw ValidationException::withMessages(['error' => $message]);
        }

        // Check user balance
        if ($request->user()->balance < $amount) {
            throw ValidationException::withMessages(['error' => __('Insufficient balance!')]);
        }
    }

    public function addCardBalance($card, $request)
    {
        // Validate request
        $this->validateTopup($request);

        // Provider instance
        $provider = $this->cardProviderMap($card->provider);

        // Add card balance
        $provider->addCardBalance($card, $request->amount);
    }

    public function updateStatus($card)
    {
        // Provider instance
        $provider = $this->cardProviderMap($card->provider);

        // Update card status
        $provider->updateCardStatus($card);
    }

    protected function cardProviderCode($provider)
    {
        $providers = [
            'Stripe Virtual Card' => 'stripe',
        ];

        return $providers[$provider];
    }
}
