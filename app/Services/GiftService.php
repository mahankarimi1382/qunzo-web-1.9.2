<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Gift;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GiftService
{
    use ApiResponseTrait, NotifyTrait;

    public function createGift($request)
    {
        $user = auth()->user();

        $isApi = $request->expectsJson();

        try {

            $limitValidation = $this->checkDailyLimit($user);

            if (isValidationException($limitValidation)) {
                return $limitValidation;
            }

            $calculationData = $this->calculateGiftAmounts($request);

            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            $this->validateUserBalance($user, $calculationData);

            DB::beginTransaction();

            $gift = $this->createGiftRecord($user, $request, $calculationData);

            $this->deductUserBalance($user, $calculationData);

            $this->processReferralBonus($user, $calculationData);

            DB::commit();

            return $gift;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return makeValidationException([
                    'message' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function redeemGift($request)
    {
        $isApi = $request->expectsJson();

        $user = auth()->user();

        try {

            $gift = $this->findAndValidateGift($request->code, $user);

            if (isValidationException($gift)) {
                return $gift;
            }

            DB::beginTransaction();

            $this->creditUserBalance($user, $gift);

            $this->createRedeemTransaction($user, $gift);

            $gift->update([
                'redeemer_id' => $user->id,
                'claimed_at' => now(),
            ]);

            $this->sendRedeemNotification($user, $gift);

            DB::commit();

            if ($isApi) {
                return [
                    'success' => true,
                    'gift' => $gift->fresh(),
                    'message' => __('Gift redeemed successfully!'),
                ];
            }

            return $gift;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }

            throw $e;
        }
    }

    private function checkDailyLimit($user)
    {
        $todayGifts = Gift::query()
            ->where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->count();

        if ($todayGifts >= setting('gift_daily_limit', 'gift')) {
            return makeValidationException([
                'amount' => [__('Today gift limit has been reached!')],
            ]);
        }
    }

    private function calculateGiftAmounts($request)
    {
        $wallet = null;
        $currency = setting('site_currency', 'global');
        $currencyId = 0;

        if ($request->wallet_id && $request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', auth()->id())
                ->where('id', $request->wallet_id)
                ->with('currency')
                ->first();

            $currency = $wallet->currency->code;
            $currencyId = $wallet->currency_id;
        }

        $charge = $this->calculateCharge($request->amount, $wallet);
        $totalAmount = $request->amount + $charge;

        $limitValidation = $this->validateAmountLimits($request->amount, $wallet);

        if (isValidationException($limitValidation)) {
            return $limitValidation;
        }

        return [
            'amount' => $request->amount,
            'charge' => $charge,
            'total_amount' => $totalAmount,
            'currency' => $currency,
            'currency_id' => $currencyId,
            'wallet' => $wallet,
        ];
    }

    private function calculateCharge($amount, $wallet)
    {
        $giftCharge = setting('gift_charge', 'gift');
        $giftChargeType = setting('gift_charge_type', 'gift');

        if ($giftChargeType === 'percentage') {
            return ($giftCharge * $amount) / 100;
        } else {
            return $wallet
                ? $giftCharge * $wallet->currency->conversion_rate
                : $giftCharge;
        }
    }

    private function validateAmountLimits($giftAmount, $wallet)
    {
        $currency = $wallet ? $wallet->currency->code : setting('site_currency', 'global');

        $minAmount = CurrencyService::getConvertedAmount(
            setting('gift_minimum', 'gift'),
            $currency,
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('gift_maximum', 'gift'),
            $currency,
            true
        );

        if ($giftAmount < $minAmount || $giftAmount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max :currency', [
                    'min' => formatAmount($minAmount, $currency),
                    'max' => formatAmount($maxAmount, $currency),
                    'currency' => $currency,
                ])],
            ]);
        }
    }

    private function validateUserBalance($user, $calculationData)
    {
        if ($calculationData['wallet']) {
            if ($calculationData['wallet']->balance < $calculationData['total_amount']) {
                throw new \Exception(__('Insufficient wallet balance!'));
            }
        } else {
            if ($user->balance < $calculationData['total_amount']) {
                throw new \Exception(__('Insufficient balance!'));
            }
        }
    }

    private function createGiftRecord($user, $request, $calculationData)
    {
        return Gift::create([
            'user_id' => $user->id,
            'amount' => $calculationData['amount'],
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['total_amount'],
            'code' => Str::uuid(),
            'currency_id' => $calculationData['currency_id'],
        ]);
    }

    private function deductUserBalance($user, $calculationData)
    {
        if ($calculationData['wallet']) {
            $calculationData['wallet']->decrement('balance', $calculationData['total_amount']);
        } else {
            $user->decrement('balance', $calculationData['total_amount']);
        }

        Transaction::create([
            'user_id' => $user->id,
            'description' => 'Gift Created',
            'type' => TxnType::GiftCode,
            'status' => TxnStatus::Success,
            'amount' => $calculationData['amount'],
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['total_amount'],
            'wallet_type' => $calculationData['wallet'] ? $calculationData['wallet']->id : 'default',
            'method' => 'System',
        ]);
    }

    private function processReferralBonus($user, $calculationData)
    {
        if (setting('create_gift', 'referral_level')) {
            $level = LevelReferral::where('type', 'create_gift')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus(
                $user,
                'create_gift',
                $calculationData['total_amount'],
                $level,
                1,
                null,
                $calculationData['wallet']
            );
        }
    }

    private function findAndValidateGift($code, $user)
    {
        $gift = Gift::where('code', $code)
            ->whereNull('redeemer_id')
            ->with(['user', 'currency'])
            ->first();

        if (! $gift) {
            return makeValidationException([
                'code' => [__('Gift code not found or already redeemed!')],
            ]);
        }

        if ($gift->user_id === $user->id) {
            return makeValidationException([
                'code' => [__('You cannot redeem your own gift!')],
            ]);
        }

        return $gift;
    }

    private function creditUserBalance($user, $gift)
    {
        if ($gift->currency_id === 0) {
            $user->increment('balance', $gift->amount);
        } else {
            $wallet = UserWallet::firstOrCreate([
                'user_id' => $user->id,
                'currency_id' => $gift->currency_id,
            ], [
                'balance' => 0,
            ]);
            $wallet->increment('balance', $gift->amount);
        }
    }

    private function createRedeemTransaction($user, $gift)
    {
        $wallet = null;
        if ($gift->currency_id !== 0) {
            $wallet = UserWallet::where('user_id', $user->id)
                ->where('currency_id', $gift->currency_id)
                ->first();
        }

        Transaction::create([
            'user_id' => $user->id,
            'description' => 'Gift Redeemed',
            'type' => TxnType::GiftRedeemed,
            'status' => TxnStatus::Success,
            'amount' => $gift->amount,
            'charge' => 0,
            'final_amount' => $gift->amount,
            'wallet_type' => $wallet ? $wallet->id : 'default',
            'method' => 'System',
        ]);
    }

    private function sendRedeemNotification($user, $gift)
    {
        $wallet = null;
        if ($gift->currency_id !== 0) {
            $wallet = UserWallet::where('user_id', $user->id)
                ->where('currency_id', $gift->currency_id)
                ->first();
        }

        $shortcodes = [
            '[[full_name]]' => $gift->user->full_name,
            '[[redeemer_name]]' => $user->full_name,
            '[[redeemer_account_no]]' => $user->account_number,
            '[[amount]]' => formatAmount($gift->amount, $gift->currency->code ?? setting('site_currency', 'global')),
            '[[gift_code]]' => $gift->code,
            '[[redeemed_at]]' => $gift->claimed_at,
            '[[gift_redeem_link]]' => Route::has('user.gift.redeem.history') ? route('user.gift.redeem.history') : '',
            '[[site_title]]' => setting('site_title', 'global'),
            '[[currency]]' => $wallet?->currency->code ?? setting('site_currency', 'global'),
        ];

        $this->sendNotify(
            $gift->user->email,
            'user_gift_redeemed',
            'User',
            $shortcodes,
            $gift->user->phone,
            $gift->user->id,
            Route::has('user.gift.redeem.history') ? route('user.gift.redeem.history') : ''
        );
    }

    public function validateCreateGift(Request $request)
    {

        if (! setting('user_gift', 'permission')) {
            return makeValidationException([
                'gift' => [__('Gift feature is not enabled!')],
            ]);
        }
        $wallet = UserWallet::where('user_id', auth()->id())
            ->where('id', $request->wallet_id)
            ->exists();

        if ($request->wallet_id != 'default' && ! $wallet) {
            return makeValidationException([
                'wallet_id' => [__('Wallet not found')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->all());
        }

        return true;
    }

    public function validateRedeemGift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:gifts,code',
        ], [
            'code.exists' => __('Gift code does not exist!'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        return true;
    }
}
