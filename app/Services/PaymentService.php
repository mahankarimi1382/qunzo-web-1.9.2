<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class PaymentService
{
    use NotifyTrait;

    public function processPayment($request)
    {
        $user = auth()->user();

        try {
            $wallet = UserWallet::where('user_id', $user->id)->where('id', $request->wallet_id)->first();
            $merchant = User::has('merchant')->where('account_number', $request->merchant_number)->first();

            if (! $merchant) {
                return makeValidationException([
                    'merchant_number' => [__('Merchant not found')],
                ]);
            }

            $chargeData = $this->calculateCharges($request->amount, $wallet);

            $paymentValidation = $this->validatePayment($request->amount, $chargeData['totalAmount'], $wallet, $user);

            if (isValidationException($paymentValidation)) {
                return $paymentValidation;
            }

            DB::beginTransaction();

            try {
                $transaction = $this->executePayment($user, $merchant, $wallet, $request->amount, $chargeData);
            } catch (\Throwable $th) {
                return makeValidationException([
                    'payment' => [__('Payment processing failed: :message', ['message' => $th->getMessage()])],
                ]);
            }

            $this->processReferralBonus($user, $transaction, $wallet);

            $this->sendPaymentNotification($user, $merchant, $transaction);

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {

            return false;
        }
    }

    private function calculateCharges($amount, $wallet)
    {
        $chargeCurrency = setting('site_currency', 'global');

        $userCharge = setting('user_make_payment_charge', 'make_payment');
        $userChargeType = setting('user_make_payment_charge_type', 'make_payment');

        if ($userChargeType === 'percentage') {
            $charge = ($userCharge * $amount) / 100;
        } else {
            $charge = $chargeCurrency == $wallet?->currency?->code || ! $wallet
                ? $userCharge
                : $userCharge * $wallet?->currency?->conversion_rate;
        }

        $merchantCharge = setting('merchant_make_payment_charge', 'make_payment');
        $merchantChargeType = setting('merchant_make_payment_charge_type', 'make_payment');

        if ($merchantChargeType === 'percentage') {
            $merchantChargeAmount = ($merchantCharge * $amount) / 100;
        } else {
            $merchantChargeAmount = $chargeCurrency == data_get($wallet, 'currency.code', setting('site_currency', 'global'))
                ? $merchantCharge
                : $merchantCharge * $wallet->currency->conversion_rate;
        }

        return [
            'userCharge' => $charge,
            'merchantCharge' => $merchantChargeAmount,
            'totalAmount' => $amount + $charge,
        ];
    }

    public function validate(Request $request)
    {
        $user = auth()->user();
        $siteCurrency = setting('site_currency', 'global');

        if (! $this->isPaymentEnabled() || ! $user->payment_status) {
            return makeValidationException([
                'user_payment' => [__('Payment currently unavailable!')],
            ]);
        }

        if (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc_payment' => [__('Please verify your KYC.')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'merchant_number' => 'required|string',
            'wallet_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->all());
        }

        $merchant = User::has('merchant')->where('account_number', $request->merchant_number)->first();
        if (! $merchant) {
            return makeValidationException([
                'merchant_number' => [__('Merchant not found')],
            ]);
        }

        if (! $merchant->status || ! $merchant->payment_status) {
            return makeValidationException([
                'merchant_number' => [__('Merchant is not available for payments')],
            ]);
        }

        $wallet = 'default';
        if ($request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', $user->id)->where('id', $request->wallet_id)->first();
            if (! $wallet) {
                return makeValidationException([
                    'wallet_id' => [__('Wallet not found')],
                ]);
            }
        }

        $amount = $request->amount;
        $minAmount = CurrencyService::getConvertedAmount(setting('payment_minimum', 'make_payment'), data_get($wallet, 'currency.code', $siteCurrency), true);
        $maxAmount = CurrencyService::getConvertedAmount(setting('payment_maximum', 'make_payment'), data_get($wallet, 'currency.code', $siteCurrency), true);

        if ($amount < $minAmount || $amount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max', [
                    'min' => $minAmount,
                    'max' => $maxAmount,
                ])],
            ]);
        }

        return true;
    }

    private function validatePayment($amount, $totalAmount, $wallet, $user)
    {
        $siteCurrency = setting('site_currency', 'global');

        $minAmount = CurrencyService::getConvertedAmount(
            setting('payment_minimum', 'make_payment'),
            data_get($wallet, 'currency.code', $siteCurrency),
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('payment_maximum', 'make_payment'),
            data_get($wallet, 'currency.code', $siteCurrency),
            true
        );

        if ($amount < $minAmount || $amount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max', [
                    'min' => $minAmount,
                    'max' => $maxAmount,
                ])],
            ]);
        }

        $hasDefaultWallet = request()->get('wallet_id') === 'default';
        $insufficientBalance = $hasDefaultWallet
            ? $user->balance < $totalAmount
            : $wallet?->balance < $totalAmount;

        if ($insufficientBalance) {
            return makeValidationException([
                'wallet_id' => [__('Insufficient balance in the selected wallet')],
            ]);
        }
    }

    private function executePayment($user, $merchant, $wallet, $amount, $chargeData)
    {
        $isDefaultWallet = request()->get('wallet_id') === 'default';
        $merchantAmount = ($amount - $chargeData['merchantCharge']);

        if ($isDefaultWallet) {
            $user->decrement('balance', $chargeData['totalAmount']);
            $merchant->increment('balance', $merchantAmount);
        } else {
            $wallet->decrement('balance', $chargeData['totalAmount']);

            $merchantWallet = UserWallet::firstOrCreate([
                'user_id' => $merchant->id,
                'currency_id' => $wallet->currency_id,
            ], [
                'balance' => 0,
            ]);

            $merchantWallet->increment('balance', $merchantAmount);
        }

        Transaction::create([
            'user_id' => $merchant->id,
            'description' => 'Payment Received',
            'from_user_id' => $user->id,
            'type' => TxnType::Payment,
            'amount' => $merchantAmount,
            'wallet_type' => request()->get('wallet_id'),
            'charge' => $chargeData['merchantCharge'],
            'final_amount' => $amount,
            'method' => 'User',
            'pay_currency' => $wallet ? $wallet->currency->code : setting('site_currency', 'global'),
            'status' => TxnStatus::Success,
        ]);

        return Transaction::create([
            'user_id' => $user->id,
            'description' => 'Payment Sent',
            'from_user_id' => $merchant->id,
            'type' => TxnType::Payment,
            'amount' => $amount,
            'wallet_type' => request()->get('wallet_id'),
            'charge' => $chargeData['userCharge'],
            'final_amount' => $chargeData['totalAmount'],
            'method' => 'User',
            'pay_currency' => $wallet ? $wallet->currency->code : setting('site_currency', 'global'),
            'status' => TxnStatus::Success,
        ]);
    }

    private function processReferralBonus($user, $transaction, $wallet)
    {
        if (setting('payment', 'referral_level')) {
            $level = LevelReferral::where('type', 'payment')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus($user, 'payment', $transaction->amount, $level, 1, null, $wallet);
        }
    }

    private function sendPaymentNotification($user, $merchant, $transaction)
    {
        $shortcodes = [
            '[[merchant_name]]' => $merchant->full_name,
            '[[amount]]' => formatAmount($transaction->amount, $transaction->currency),
            '[[charge]]' => formatAmount($transaction->charge, $transaction->currency),
            '[[total_amount]]' => formatAmount($transaction->final_amount, $transaction->currency),
            '[[wallet]]' => data_get($transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
            '[[gateway]]' => $transaction->method,
            '[[payment_at]]' => $transaction->created_at,
            '[[payment_id]]' => $transaction->tnx,
            '[[user_name]]' => $user->full_name,
            '[[user_account_no]]' => $user->account_number,
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $merchant->email,
            'merchant_payment',
            'Merchant',
            $shortcodes,
            $merchant->phone,
            $merchant->id,
            Route::has('merchant.transactions') ? route('merchant.transactions') : ''
        );
    }

    public function isPaymentEnabled()
    {
        return setting('user_payment', 'permission');
    }
}
