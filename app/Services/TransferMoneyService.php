<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class TransferMoneyService
{
    use NotifyTrait;

    public function transferMoney($request)
    {
        $senderUser = auth()->user();
        $isApi = $request->expectsJson();

        try {

            $availabilityCheck = $this->checkAvailability($senderUser);
            if (isValidationException($availabilityCheck)) {
                return $availabilityCheck;
            }

            $recipient = $this->findRecipient($request->account_number, $senderUser->id);
            if (isValidationException($recipient)) {
                return $recipient;
            }

            $calculationData = $this->calculateTransferAmounts($request);
            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            $dailyLimitCheck = $this->validateDailyLimit($senderUser, $calculationData);
            if (isValidationException($dailyLimitCheck)) {
                return $dailyLimitCheck;
            }

            $balanceValidation = $this->validateSenderBalance($senderUser, $calculationData);
            if (isValidationException($balanceValidation)) {
                return $balanceValidation;
            }

            DB::beginTransaction();

            $transferResult = $this->processTransfer($senderUser, $recipient, $calculationData, $request);

            $this->processReferralBonus($senderUser, $calculationData);

            $this->sendTransferNotification($senderUser, $recipient, $calculationData);

            DB::commit();

            return $transferResult;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return makeValidationException([
                    'message' => [$e->getMessage()],
                ]);
            }

            throw $e;
        }
    }

    public function checkAvailability($user)
    {
        if (! setting('user_transfer', 'permission')) {
            return makeValidationException([
                'transfer' => [__('Transfer feature is disabled')],
            ]);
        }

        if (! $user->transfer_status) {
            return makeValidationException([
                'transfer' => [__('Your transfer permission is disabled')],
            ]);
        }

        if (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc' => [__('Please verify your KYC to transfer money')],
            ]);
        }
    }

    public function findRecipient($accountNumber, $currentUserId)
    {
        $recipient = User::user()
            ->where('account_number', $accountNumber)
            ->whereNot('id', $currentUserId)
            ->first();

        if (! $recipient) {
            return makeValidationException([
                'account_number' => [__('Recipient user does not exist!')],
            ]);
        }

        if (! $recipient->status) {
            return makeValidationException([
                'account_number' => [__('Recipient account is inactive!')],
            ]);
        }

        return $recipient;
    }

    public function calculateTransferAmounts($request)
    {
        $wallet = null;
        $currency = setting('site_currency', 'global');

        if ($request->wallet_id && $request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', auth()->id())
                ->where('id', $request->wallet_id)
                ->with('currency')
                ->first();

            if (! $wallet) {
                return makeValidationException([
                    'wallet_id' => [__('Selected wallet not found!')],
                ]);
            }

            $currency = $wallet->currency->code;
        }

        $charge = $this->calculateCharge($request->amount, $wallet);
        $finalAmount = $request->amount + $charge;

        $limitValidation = $this->validateAmountLimits($request->amount, $wallet);
        if (isValidationException($limitValidation)) {
            return $limitValidation;
        }

        return [
            'amount' => $request->amount,
            'charge' => $charge,
            'final_amount' => $finalAmount,
            'currency' => $currency,
            'wallet' => $wallet,
        ];
    }

    public function calculateCharge($amount, $wallet)
    {
        $transferCharge = setting('transfer_charge', 'transfer');
        $transferChargeType = setting('transfer_charge_type', 'transfer');

        if ($transferChargeType === 'percentage') {
            return ($transferCharge * $amount) / 100;
        } else {
            return $wallet
                ? $transferCharge * $wallet->currency->conversion_rate
                : $transferCharge;
        }
    }

    public function validateAmountLimits($finalAmount, $wallet)
    {
        $currency = $wallet ? $wallet->currency->code : setting('site_currency', 'global');

        $minAmount = CurrencyService::getConvertedAmount(
            setting('transfer_minimum', 'transfer'),
            $currency,
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('transfer_maximum', 'transfer'),
            $currency,
            true
        );

        if ($finalAmount < $minAmount || $finalAmount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max :currency', [
                    'min' => $minAmount,
                    'max' => $maxAmount,
                    'currency' => $currency,
                ])],
            ]);
        }
    }

    public function validateDailyLimit($user, $calculationData)
    {
        $todayTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TxnType::SendMoney)
            ->whereDate('created_at', now())
            ->sum('amount');

        $dailyLimit = setting('transfer_daily_limit', 'transfer');

        if (($todayTransactions + $calculationData['amount']) > $dailyLimit) {
            return makeValidationException([
                'amount' => [__('Transfer amount exceeds daily limit. Remaining limit: :amount', [
                    'amount' => $dailyLimit - $todayTransactions,
                ])],
            ]);
        }
    }

    public function validateSenderBalance($user, $calculationData)
    {
        if ($calculationData['wallet']) {
            if ($calculationData['wallet']->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [__('Insufficient wallet balance!')],
                ]);
            }
        } else {
            if ($user->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [__('Insufficient balance!')],
                ]);
            }
        }
    }

    public function processTransfer($senderUser, $recipient, $calculationData, $request)
    {
        $walletId = $calculationData['wallet'] ? $calculationData['wallet']->id : 'default';

        $senderTxn = (new Txn)->new(
            $calculationData['amount'],
            $calculationData['charge'],
            $calculationData['final_amount'],
            $walletId,
            'System',
            'Transfer to '.$recipient->full_name,
            TxnType::SendMoney,
            TxnStatus::Success,
            $calculationData['currency'],
            $calculationData['final_amount'],
            $senderUser->id,
            null,
            'User',
            []
        );

        $receiverTxn = (new Txn)->new(
            $calculationData['amount'],
            0,
            $calculationData['amount'],
            $walletId,
            'System',
            'Received money from '.$senderUser->full_name,
            TxnType::ReceiveMoney,
            TxnStatus::Success,
            $calculationData['currency'],
            $calculationData['amount'],
            $recipient->id,
            $senderUser->id,
            'User',
            []
        );

        if ($calculationData['wallet']) {

            $calculationData['wallet']->decrement('balance', $calculationData['final_amount']);

            $recipientWallet = UserWallet::firstOrCreate([
                'user_id' => $recipient->id,
                'currency_id' => $calculationData['wallet']->currency_id,
            ], [
                'balance' => 0,
            ]);

            $recipientWallet->increment('balance', $calculationData['amount']);
        } else {

            $senderUser->decrement('balance', $calculationData['final_amount']);

            $recipient->increment('balance', $calculationData['amount']);
        }

        return [
            'sender_transaction' => $senderTxn,
            'receiver_transaction' => $receiverTxn,
        ];
    }

    public function processReferralBonus($user, $calculationData)
    {
        if (setting('transfer', 'referral_level')) {
            $level = LevelReferral::where('type', 'transfer')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus(
                $user,
                'transfer',
                $calculationData['amount'],
                $level,
                1,
                null,
                $calculationData['wallet']
            );
        }
    }

    public function sendTransferNotification($senderUser, $recipient, $calculationData)
    {
        $shortcodes = [
            '[[full_name]]' => $senderUser->full_name,
            '[[amount]]' => formatAmount($calculationData['amount'], $calculationData['currency']),
            '[[currency]]' => $calculationData['currency'],
            '[[sender_name]]' => $senderUser->full_name,
            '[[sender_account_no]]' => $senderUser->account_number,
            '[[transaction_link]]' => Route::has('user.transferMoney.history') ? route('user.transferMoney.history') : '',
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $recipient->email,
            'user_receive_money',
            'User',
            $shortcodes,
            $recipient->phone,
            $recipient->id,
            Route::is('user.transferMoney.history') ? route('user.transferMoney.history') : ''
        );
    }

    public function validateTransfer(Request $request)
    {

        if (! setting('user_transfer', 'permission')) {
            return makeValidationException([
                'transfer' => [__('Transfer feature is not enabled!')],
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
            'account_number' => 'required|exists:users,account_number',
            'amount' => 'required|numeric',
            'wallet_id' => setting('multiple_currency', 'permission') ? 'required' : 'nullable',
        ], [
            'account_number.exists' => __('Recipient account number does not exist'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        return true;
    }

    public function getTransferHistory($request, $type = 'all')
    {
        $user = auth()->user();

        $query = Transaction::query()
            ->with(['userWallet.currency', 'fromUser', 'user'])
            ->where('user_id', $user->id);

        switch ($type) {
            case 'send':
                $query->where('type', TxnType::SendMoney);
                break;
            case 'receive':
                $query->where('type', TxnType::ReceiveMoney);
                break;
            default:
                $query->whereIn('type', [TxnType::SendMoney, TxnType::ReceiveMoney]);
                break;
        }

        $query->when($request->tnx_id, fn ($q) => $q->where('tnx', $request->tnx_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        return $query->latest();
    }
}
