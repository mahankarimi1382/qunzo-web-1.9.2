<?php

namespace App\Services;

use App\Enums\RequestMoneyStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\LevelReferral;
use App\Models\MoneyRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestMoneyService
{
    use NotifyTrait;

    public function createRequest($request)
    {
        $user = auth()->user();

        try {

            $this->checkDailyLimit($user);

            $recipient = $this->findRecipient($request->request_to, $user->id);
            if (isValidationException($recipient)) {
                return $recipient;
            }

            $calculationData = $this->calculateRequestAmounts($request, $recipient);
            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            DB::beginTransaction();

            $moneyRequest = $this->createMoneyRequest($user, $recipient, $request, $calculationData);

            if (isValidationException($moneyRequest)) {
                return $moneyRequest;
            }

            $this->sendRequestNotification($user, $recipient, $moneyRequest, $request);

            DB::commit();

            return $moneyRequest;
        } catch (\Exception $e) {
            DB::rollBack();

            return isValidationException($e) ? $e : false;
        }
    }

    public function processRequestAction($requestId, $action)
    {
        $user = auth()->user();

        $moneyRequest = MoneyRequest::where('recipient_user_id', $user->id)
            ->where('status', RequestMoneyStatus::Pending)
            ->find($requestId);

        if (! $moneyRequest) {
            return makeValidationException([
                'request' => [__('Request not found!')],
            ]);
        }

        if ($action === 'accept') {
            $result = $this->acceptRequest($moneyRequest);
        } elseif ($action === 'reject') {
            $result = $this->rejectRequest($moneyRequest);
        }
        if ($result instanceof \Exception) {
            return makeValidationException([
                'action' => [$result->getMessage()],
            ]);
        } else {
            return $result;
        }
    }

    private function acceptRequest($moneyRequest)
    {
        try {

            $this->validateRecipientBalance($moneyRequest);

            DB::beginTransaction();

            $this->processRequestPayment($moneyRequest);

            $moneyRequest->update(['status' => RequestMoneyStatus::Success]);

            $this->processReferralBonus($moneyRequest);

            DB::commit();

            return $moneyRequest;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    private function rejectRequest($moneyRequest)
    {
        $moneyRequest->update(['status' => RequestMoneyStatus::Rejected]);

        return $moneyRequest;
    }

    private function checkDailyLimit($user)
    {
        $todayRequests = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TxnType::RequestMoney)
            ->whereDate('created_at', now())
            ->count();

        if ($todayRequests >= setting('request_money_daily_limit', 'request_money')) {
            return makeValidationException([
                'amount' => [__('Today request limit has been reached!')],
            ]);
        }
    }

    private function findRecipient($accountNumber, $currentUserId)
    {
        $recipient = User::user()
            ->whereNot('id', $currentUserId)
            ->where('account_number', $accountNumber)
            ->first();

        if (! $recipient) {
            return makeValidationException([
                'request_to' => [__('Recipient user does not exist!')],
            ]);
        }

        if (! $recipient->status) {
            return makeValidationException([
                'request_to' => [__('Recipient account is inactive!')],
            ]);
        }

        return $recipient;
    }

    private function calculateRequestAmounts($request, $recipient)
    {
        $wallet = null;
        $currency = setting('site_currency', 'global');

        if ($request->wallet_id && $request->wallet_id !== 'default') {
            $wallet = UserWallet::with('currency')->where('user_id', auth()->id())
                ->where('id', $request->wallet_id)
                ->first();

            if (! $wallet) {
                return makeValidationException([
                    'wallet_id' => [__('Selected wallet not found!')],
                ]);
            }

            $currency = $wallet->currency->code;
        }

        $validation = $this->validateAmountLimits($request->amount, $wallet);

        if (isValidationException($validation)) {
            return $validation;
        }

        $charge = $this->calculateCharge($request->amount, $wallet);

        return [
            'amount' => $request->amount,
            'charge' => $charge,
            'final_amount' => $request->amount + $charge,
            'currency' => $currency,
            'wallet' => $wallet,
        ];
    }

    private function validateAmountLimits($amount, $wallet)
    {
        $currency = $wallet ? $wallet->currency->code : setting('site_currency', 'global');

        $minAmount = currency()->getConvertedAmount(setting('request_money_minimum', 'request_money'), $currency, true);

        $maxAmount = currency()->getConvertedAmount(setting('request_money_maximum', 'request_money'), $currency, true);

        if ($amount < $minAmount || $amount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max :currency', [
                    'min' => $minAmount,
                    'max' => $maxAmount,
                    'currency' => $currency,
                ])],
            ]);
        }
    }

    private function calculateCharge($amount, $wallet)
    {
        $charge = 0;
        $requestMoneyCharge = setting('request_money_charge', 'request_money');
        $requestMoneyChargeType = setting('request_money_charge_type', 'request_money');

        if ($requestMoneyChargeType === 'percentage') {
            $charge = ($requestMoneyCharge * $amount) / 100;
        } else {
            $charge = $wallet
                ? (new CurrencyService)->convert($requestMoneyCharge, $wallet->currency->code, setting('site_currency', 'global'))
                : $requestMoneyCharge;
        }

        return $charge;
    }

    private function createMoneyRequest($user, $recipient, $request, $calculationData)
    {
        return MoneyRequest::create([
            'requester_user_id' => $user->id,
            'recipient_user_id' => $recipient->id,
            'currency' => $calculationData['currency'],
            'amount' => $calculationData['amount'],
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['final_amount'],
            'status' => RequestMoneyStatus::Pending,
            'note' => $request->note,
        ]);
    }

    private function validateRecipientBalance($moneyRequest)
    {
        $recipient = $moneyRequest->recipient;
        $amount = $moneyRequest->final_amount;

        if ($moneyRequest->currency === setting('site_currency', 'global')) {
            if ($recipient?->balance < $amount) {
                return makeValidationException([
                    'amount' => [__('Insufficient funds!')],
                ]);
            }
        } else {
            $recipientWallet = UserWallet::where('user_id', $recipient->id)
                ->whereRelation('currency', 'code', $moneyRequest->currency)
                ->first();

            if (! $recipientWallet || $recipientWallet?->balance < $amount) {
                return makeValidationException([
                    'amount' => [__('Insufficient funds in selected currency!')],
                ]);
            }
        }
    }

    private function processRequestPayment($moneyRequest)
    {
        $recipient = $moneyRequest->recipient;
        $requester = $moneyRequest->requester;
        $amount = $moneyRequest->final_amount;
        $requestAmount = $moneyRequest->amount;

        // Check the requester available or not
        if (! $requester) {
            return makeValidationException([
                'requester' => [__('Requester not found!')],
            ]);
        }

        // Store conditional wallets
        $recipientWallet = null;
        $requesterWallet = null;

        if ($moneyRequest->currency == setting('site_currency', 'global')) {
            $recipient->decrement('balance', $amount);
            $recipientWallet = null;
        } else {
            $recipientWallet = UserWallet::where('user_id', $recipient->id)
                ->whereRelation('currency', 'code', $moneyRequest->currency)
                ->first();
            $recipientWallet->decrement('balance', $amount);
        }

        Transaction::create([
            'user_id' => $recipient->id,
            'description' => 'Money Request Accepted',
            'wallet_type' => $recipientWallet ? $recipientWallet->id : 'default',
            'type' => TxnType::RequestMoney,
            'amount' => $requestAmount,
            'charge' => $moneyRequest->charge,
            'final_amount' => $amount,
            'method' => 'User',
            'status' => TxnStatus::Success,
        ]);

        if ($moneyRequest->currency == setting('site_currency', 'global')) {
            $requester->increment('balance', $requestAmount);
            $requesterWallet = null;
        } else {
            $requesterWallet = UserWallet::firstOrCreate([
                'user_id' => $requester->id,
                'currency_id' => $recipientWallet->currency_id,
            ], [
                'balance' => 0,
            ]);
            $requesterWallet->increment('balance', $requestAmount);
        }

        $transaction = Transaction::create([
            'user_id' => $requester->id,
            'from_user_id' => $recipient->id,
            'description' => 'Money Request Accepted',
            'wallet_type' => $requesterWallet ? $requesterWallet->id : 'default',
            'type' => TxnType::ReceiveMoney,
            'amount' => $requestAmount,
            'charge' => 0,
            'final_amount' => $requestAmount,
            'method' => 'User',
            'status' => TxnStatus::Success,
        ]);

        $this->sendRequestAcceptedNotification($moneyRequest);

        return $transaction;
    }

    private function processReferralBonus($moneyRequest)
    {
        if (setting('request_money', 'referral_level')) {
            $level = LevelReferral::where('type', 'request_money')->max('the_order') + 1;
            $requesterWallet = null;
            if ($moneyRequest->currency !== setting('site_currency', 'global')) {
                $requesterWallet = UserWallet::where('user_id', $moneyRequest->requester_user_id)
                    ->whereRelation('currency', 'code', $moneyRequest->currency)
                    ->first();
            }

            creditCurrencyWiseReferralBonus(
                $moneyRequest->requester,
                'request_money',
                $moneyRequest->amount,
                $level,
                1,
                null,
                $requesterWallet
            );
        }
    }

    private function sendRequestAcceptedNotification($moneyRequest)
    {
        $shortcodes = [
            '[[full_name]]' => $moneyRequest->requester->full_name,
            '[[amount]]' => formatAmount($moneyRequest->amount, $moneyRequest->currency),
            '[[currency]]' => $moneyRequest->currency,
            '[[trx_id]]' => $moneyRequest->id,
            '[[date]]' => $moneyRequest->created_at->format('d M Y'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
            '[[sender_name]]' => $moneyRequest->recipient->full_name,
            '[[sender_note]]' => $moneyRequest->note ?? '',
            '[[sender_wallet]]' => $moneyRequest->currency,
            '[[sender_account_no]]' => $moneyRequest->recipient->account_number,
            '[[request_money_link]]' => '',
        ];

        $this->sendNotify(
            $moneyRequest->requester->email,
            'user_request_money_accepted',
            'User',
            $shortcodes,
            $moneyRequest->requester->phone,
            $moneyRequest->requester->id,
            ''
        );
    }

    private function sendRequestNotification($user, $recipient, $moneyRequest, $request)
    {
        $shortcodes = [
            '[[full_name]]' => $recipient->full_name,
            '[[amount]]' => formatAmount($moneyRequest->amount, $moneyRequest->currency),
            '[[charge]]' => formatAmount($moneyRequest->charge, $moneyRequest->currency),
            '[[total_amount]]' => formatAmount($moneyRequest->final_amount, $moneyRequest->currency),
            '[[sender_name]]' => $user->full_name,
            '[[sender_note]]' => $request->note ?? '',
            '[[sender_wallet]]' => $moneyRequest->currency,
            '[[sender_account_no]]' => $user->account_number,
            '[[request_money_link]]' => '',
            '[[site_title]]' => setting('site_title', 'global'),
            '[[currency]]' => $moneyRequest->currency,
        ];

        $this->sendNotify(
            $recipient->email,
            'user_request_money',
            'User',
            $shortcodes,
            $recipient->phone,
            $recipient->id,
            ''
        );
    }

    public function validate(Request $request)
    {

        if (! setting('user_request_money', 'permission')) {
            return makeValidationException([
                'request_money' => [__('Request money feature is not enabled!')],
            ]);
        }
        $user = auth()->user();
        $recipient = User::user()
            ->whereNot('id', $user->id)
            ->where('account_number', $request->request_to)
            ->first();

        if (! $recipient) {
            return makeValidationException([
                'request_to' => [__('Recipient user does not exist!')],
            ]);
        } elseif ($recipient->id === $user->id) {
            return makeValidationException([
                'request_to' => [__('You cannot request money from yourself!')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required',
            'request_to' => 'required|string',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->all());
        }

        return true;
    }
}
