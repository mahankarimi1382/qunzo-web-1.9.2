<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExchangeService
{
    use NotifyTrait;

    public function exchange($request)
    {
        $user = $request->user();

        try {

            $availabilityCheck = $this->checkAvailability($user);
            if (isValidationException($availabilityCheck)) {
                return $availabilityCheck;
            }

            $calculationData = $this->calculateExchangeAmounts($request);
            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            $balanceValidation = $this->validateUserBalance($user, $calculationData);
            if (isValidationException($balanceValidation)) {
                return $balanceValidation;
            }

            DB::beginTransaction();

            $exchangeResult = $this->processExchange($user, $calculationData, $request);

            $this->processReferralBonus($user, $calculationData);

            DB::commit();

            return $exchangeResult;
        } catch (\Exception $e) {
            DB::rollBack();

            return makeValidationException([
                'message' => [$e->getMessage()],
            ]);
        }
    }

    public function checkAvailability($user)
    {
        if (! setting('user_exchange', 'permission')) {
            return makeValidationException([
                'exchange' => [__('Currency exchange feature is disabled')],
            ]);
        }

        if (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc' => [__('Please verify your KYC to use exchange feature')],
            ]);
        }

        return true;
    }

    public function calculateExchangeAmounts($request)
    {
        $fromWallet = null;
        $toWallet = null;
        $fromCurrency = null;
        $toCurrency = null;
        $siteCurrency = setting('site_currency', 'global');

        if ($request->from_wallet === 'default') {
            $fromCurrency = $siteCurrency;
        } else {
            $fromWallet = UserWallet::find($request->from_wallet);

            if (! $fromWallet || ! $fromWallet->currency) {
                return makeValidationException([
                    'from_wallet' => [__('From wallet is not found!')],
                ]);
            }

            $fromCurrency = $fromWallet->currency?->code;
        }

        if ($request->to_wallet === 'default') {
            $toCurrency = $siteCurrency;
        } else {
            $toWallet = UserWallet::find($request->to_wallet);
            if (! $toWallet || ! $toWallet->currency) {
                return makeValidationException([
                    'to_wallet' => [__('To wallet is not found!')],
                ]);
            }

            $toCurrency = $toWallet->currency?->code;
        }

        $charge = $this->calculateCharge($request->amount, $fromWallet);
        $finalAmount = $request->amount + $charge;

        $limitValidation = $this->validateAmountLimits($request->amount, $fromCurrency);
        if (isValidationException($limitValidation)) {
            return $limitValidation;
        }

        $convertedAmount = $this->calculateConvertedAmount($request->amount, $fromCurrency, $toCurrency);

        return [
            'amount' => $request->amount,
            'charge' => $charge,
            'final_amount' => $finalAmount,
            'converted_amount' => $convertedAmount,
            'from_wallet' => $fromCurrency === $siteCurrency ? 'default' : $fromWallet,
            'to_wallet' => $toCurrency === $siteCurrency ? 'default' : $toWallet,
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
        ];
    }

    public function calculateCharge($amount, $wallet)
    {
        $exchangeCharge = setting('exchange_charge', 'exchange');
        $exchangeChargeType = setting('exchange_charge_type', 'exchange');

        if ($exchangeChargeType === 'percentage') {
            return ($exchangeCharge * $amount) / 100;
        } else {
            return $wallet
                ? $exchangeCharge * $wallet->currency->conversion_rate
                : $exchangeCharge;
        }
    }

    public function validateAmountLimits($amount, $currency)
    {
        $minAmount = CurrencyService::getConvertedAmount(
            setting('exchange_minimum', 'exchange'),
            $currency,
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('exchange_maximum', 'exchange'),
            $currency,
            true
        );

        if ($amount < $minAmount || $amount > $maxAmount) {
            return makeValidationException([
                'amount' => [
                    __('Please enter the amount within the range :min to :max :currency', [
                        'min' => $minAmount,
                        'max' => $maxAmount,
                        'currency' => $currency,
                    ]),
                ],
            ]);
        }

        return true;
    }

    public function calculateConvertedAmount($amount, $fromCurrency, $toCurrency)
    {
        return CurrencyService::convert($amount, $fromCurrency, $toCurrency);
    }

    public function validateUserBalance($user, $calculationData)
    {
        if ($calculationData['from_wallet'] instanceof UserWallet) {
            if ($calculationData['from_wallet']->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [
                        __('Insufficient balance in :currency wallet', [
                            'currency' => $calculationData['from_wallet']->currency->name,
                        ]),
                    ],
                ]);
            }
        } else {
            if ($user->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [__('Insufficient balance in main wallet')],
                ]);
            }
        }

        return true;
    }

    public function processExchange($user, $calculationData, $request)
    {
        if ($calculationData['from_wallet'] == 'default') {
            $user->decrement('balance', $calculationData['final_amount']);
        } else {
            $calculationData['from_wallet']->decrement('balance', $calculationData['final_amount']);
        }

        if ($calculationData['to_wallet'] == 'default') {
            $user->increment('balance', $calculationData['converted_amount']);
            $toWalletId = 'default';
        } else {
            $toUserWallet = $calculationData['to_wallet'];
            $toUserWallet->increment('balance', $calculationData['converted_amount']);
            $toWalletId = $toUserWallet->id;
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_type' => $toWalletId,
            'from_model' => 'User',
            'description' => $this->generateExchangeDescription($calculationData),
            'amount' => $calculationData['converted_amount'],
            'type' => TxnType::Exchange,
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['converted_amount'],
            'method' => 'System',
            'pay_currency' => $calculationData['from_currency'],
            'pay_amount' => $calculationData['amount'],
            'status' => TxnStatus::Success,
        ]);

        return [
            'transaction' => $transaction,
            'exchange_data' => $calculationData,
        ];
    }

    private function generateExchangeDescription($calculationData)
    {
        $fromName = $calculationData['from_wallet'] != 'default'
            ? $calculationData['from_wallet']?->currency?->name
            : 'Main Wallet';

        $toName = $calculationData['to_wallet'] == 'default'
            ? 'Main Wallet'
            : $calculationData['to_wallet']?->currency?->name;

        return "Exchange {$fromName} To {$toName}";
    }

    public function processReferralBonus($user, $calculationData)
    {
        if (setting('exchange', 'referral_level')) {
            $level = LevelReferral::where('type', 'exchange')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus(
                $user,
                'exchange',
                $calculationData['final_amount'],
                $level,
                1,
                null,
                $calculationData['from_wallet']
            );
        }
    }

    public function validateExchange(Request $request)
    {
        if (! setting('user_exchange', 'permission')) {
            return makeValidationException([
                'exchange' => [__('Exchange feature is not enabled!')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'from_wallet' => 'required',
            'to_wallet' => 'required|different:from_wallet',
        ], [
            'to_wallet.different' => __('From and to currencies must be different'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        if ($request->from_wallet !== 'default') {
            $fromWallet = UserWallet::find($request->from_wallet);

            if (! $fromWallet || ! $fromWallet->currency) {
                return makeValidationException([
                    'from_wallet' => [__('From currency or wallet not found')],
                ]);
            }
        }

        if ($request->to_wallet !== 'default') {
            $toWallet = UserWallet::find($request->to_wallet);
            if (! $toWallet || ! $toWallet->currency) {
                return makeValidationException([
                    'to_wallet' => [__('To wallet or currency not found')],
                ]);
            }
        }

        return true;
    }

    public function getExchangeHistory($request)
    {
        $user = $request->user();

        $query = Transaction::query()
            ->with(['userWallet.currency'])
            ->where('user_id', $user->id)
            ->where('type', TxnType::Exchange);

        $query->when($request->tnx_id, fn ($q) => $q->where('tnx', $request->tnx_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        return $query->latest();
    }
}
