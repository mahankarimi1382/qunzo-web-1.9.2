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
use Illuminate\Support\Facades\Validator;

class CashoutService
{
    use NotifyTrait;

    public function cashout($request)
    {
        $user = auth()->user();
        $isApi = $request->expectsJson();

        try {

            $availabilityCheck = $this->checkAvailability($user);
            if (isValidationException($availabilityCheck)) {
                return $availabilityCheck;
            }

            $agent = $this->findAgent($request->agent_number, $user->id);
            if (isValidationException($agent)) {
                return $agent;
            }

            $calculationData = $this->calculateCashoutAmounts($request);
            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            $limitCheck = $this->validateLimits($user, $calculationData);
            if (isValidationException($limitCheck)) {
                return $limitCheck;
            }

            $balanceValidation = $this->validateUserBalance($user, $calculationData);
            if (isValidationException($balanceValidation)) {
                return $balanceValidation;
            }

            DB::beginTransaction();

            $cashoutResult = $this->processCashout($user, $agent, $calculationData, $request);

            $this->processReferralBonus($user, $calculationData);

            $this->processAgentCommission($calculationData['amount'], $agent, $user, $calculationData);

            DB::commit();

            return $cashoutResult;
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
        if (! setting('user_cashout', 'permission')) {
            return makeValidationException([
                'cashout' => [__('Cashout feature is disabled')],
            ]);
        }
    }

    public function findAgent($agentNumber, $currentUserId)
    {
        $agent = User::has('agent')
            ->where('account_number', $agentNumber)
            ->whereNot('id', $currentUserId)
            ->first();

        if (! $agent) {
            return makeValidationException([
                'agent_number' => [__('Agent not found!')],
            ]);
        }

        if (! $agent->status) {
            return makeValidationException([
                'agent_number' => [__('Agent account is inactive!')],
            ]);
        }

        return $agent;
    }

    public function calculateCashoutAmounts($request)
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
        $cashoutCharge = setting('cashout_charge', 'cashout');
        $cashoutChargeType = setting('cashout_charge_type', 'cashout');

        if ($cashoutChargeType === 'percentage') {
            return ($cashoutCharge * $amount) / 100;
        } else {
            return $wallet
                ? $cashoutCharge * $wallet->currency->conversion_rate
                : $cashoutCharge;
        }
    }

    public function validateAmountLimits($amount, $wallet)
    {
        $currency = $wallet ? $wallet->currency->code : setting('site_currency', 'global');

        $minAmount = CurrencyService::getConvertedAmount(
            setting('cashout_minimum', 'cashout'),
            $currency,
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('cashout_maximum', 'cashout'),
            $currency,
            true
        );

        if ($amount < $minAmount || $amount > $maxAmount) {
            return makeValidationException([
                'amount' => [__('Please enter the amount within the range :min to :max :currency', [
                    'min' => formatAmount($minAmount, $currency),
                    'max' => formatAmount($maxAmount, $currency),
                    'currency' => $currency,
                ])],
            ]);
        }
    }

    public function validateLimits($user, $calculationData)
    {
        $wallet = $calculationData['wallet'];
        $currency = $calculationData['currency'];
        $walletType = $wallet ? $wallet->id : 'default';

        $todayTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TxnType::CashOut)
            ->where('wallet_type', $walletType)
            ->whereDate('created_at', now())
            ->sum('amount');

        $dailyLimit = CurrencyService::getConvertedAmount(
            setting('cashout_daily_limit', 'cashout'),
            $currency,
            true
        );

        if (($todayTransactions + $calculationData['amount']) > $dailyLimit) {
            return makeValidationException([
                'amount' => [__('Cashout amount exceeds daily limit. Remaining limit: :amount', [
                    'amount' => formatAmount($dailyLimit - $todayTransactions, $currency),
                ])],
            ]);
        }

        $monthlyTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TxnType::CashOut)
            ->where('wallet_type', $walletType)
            ->whereMonth('created_at', now())
            ->sum('amount');

        $monthlyLimit = CurrencyService::getConvertedAmount(
            setting('cashout_monthly_limit', 'cashout'),
            $currency,
            true
        );

        if (($monthlyTransactions + $calculationData['amount']) > $monthlyLimit) {
            return makeValidationException([
                'amount' => [__('Cashout amount exceeds monthly limit. Remaining limit: :amount', [
                    'amount' => formatAmount($monthlyLimit - $monthlyTransactions, $currency),
                ])],
            ]);
        }
    }

    public function validateUserBalance($user, $calculationData)
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

    public function processCashout($user, $agent, $calculationData, $request)
    {
        $walletId = $calculationData['wallet'] ? $calculationData['wallet']->id : 'default';

        if ($calculationData['wallet']) {
            $calculationData['wallet']->decrement('balance', $calculationData['final_amount']);
        } else {
            $user->decrement('balance', $calculationData['final_amount']);
        }

        if ($calculationData['wallet']) {
            $agentWallet = UserWallet::firstOrCreate([
                'user_id' => $agent->id,
                'currency_id' => $calculationData['wallet']->currency_id,
            ], [
                'balance' => 0,
            ]);
            $agentWallet->increment('balance', $calculationData['amount']);
        } else {
            $agent->increment('balance', $calculationData['amount']);
        }

        $userTransaction = Transaction::create([
            'user_id' => $user->id,
            'description' => 'Cashout',
            'from_user_id' => $agent->id,
            'type' => TxnType::CashOut,
            'amount' => $calculationData['amount'],
            'wallet_type' => $walletId,
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['final_amount'],
            'method' => 'User',
            'status' => TxnStatus::Success,
        ]);

        $agentTransaction = Transaction::create([
            'user_id' => $agent->id,
            'description' => 'Cash Received from '.$user->full_name,
            'from_user_id' => $user->id,
            'type' => TxnType::CashReceived,
            'amount' => $calculationData['amount'],
            'wallet_type' => $walletId,
            'charge' => 0,
            'final_amount' => $calculationData['amount'],
            'method' => 'User',
            'status' => TxnStatus::Success,
        ]);

        $this->sendAgentCashoutNotification($agent, $user, $calculationData, $agentTransaction);

        return [
            'user_transaction' => $userTransaction,
            'agent_transaction' => $agentTransaction,
        ];
    }

    public function processReferralBonus($user, $calculationData)
    {
        if (setting('cashout', 'referral_level')) {
            $level = LevelReferral::where('type', 'cashout')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus(
                $user,
                'cashout',
                $calculationData['amount'],
                $level,
                1,
                null,
                $calculationData['wallet']
            );
        }
    }

    public function processAgentCommission($cashoutAmount, $agent, $user, $calculationData)
    {
        $commission = 0;
        $cashoutAgentCommission = setting('cashout_agent_commission', 'cashout');
        $cashoutAgentCommissionType = setting('cashout_agent_commission_type', 'cashout');

        if ($cashoutAgentCommissionType === 'percentage') {
            $commission = ($cashoutAgentCommission * $cashoutAmount) / 100;
        } else {
            $commission = $calculationData['wallet']
                ? $cashoutAgentCommission * $calculationData['wallet']->currency->conversion_rate
                : $cashoutAgentCommission;
        }

        $agentCommissionTxn = Transaction::create([
            'user_id' => $agent->id,
            'description' => 'Cashout Commission',
            'type' => TxnType::CashoutCommission,
            'from_user_id' => $user->id,
            'from_model' => 'User',
            'amount' => $commission,
            'wallet_type' => $calculationData['wallet']->id ?? 'default',
            'charge' => 0,
            'final_amount' => $commission,
            'method' => 'System',
            'status' => TxnStatus::Success,
        ]);

        if ($calculationData['wallet']) {
            $agentWallet = UserWallet::firstOrCreate([
                'user_id' => $agent->id,
                'currency_id' => $calculationData['wallet']->currency_id,
            ], [
                'balance' => 0,
            ]);
            $agentWallet->increment('balance', $commission);
        } else {
            $agent->increment('balance', $commission);
        }

        $this->sendAgentNotification($agent, $commission, $calculationData, $agentCommissionTxn);
    }

    private function sendAgentNotification($agent, $commission, $calculationData, $transaction)
    {
        $shortcodes = [
            '[[full_name]]' => $agent->full_name,
            '[[amount]]' => formatAmount($commission, $calculationData['currency']),
            '[[wallet]]' => $calculationData['wallet'] ? $calculationData['wallet']->currency->name : 'Default',
            '[[commission_at]]' => now(),
            '[[txn_id]]' => $transaction->tnx,
            '[[transaction_link]]' => '',
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $agent->email,
            'agent_commission',
            'Agent',
            $shortcodes,
            $agent->phone,
            $agent->id,
            ''
        );
    }

    private function sendAgentCashoutNotification($agent, $user, $calculationData, $transaction)
    {
        $shortcodes = [
            '[[full_name]]' => $agent->full_name,
            '[[user_name]]' => $user->full_name,
            '[[user_account_no]]' => $user->account_number,
            '[[amount]]' => formatAmount($calculationData['amount'], $calculationData['currency']),
            '[[currency]]' => $calculationData['currency'],
            '[[charge]]' => formatAmount($calculationData['charge'], $calculationData['currency']),
            '[[total_amount]]' => formatAmount($calculationData['final_amount'], $calculationData['currency']),
            '[[wallet]]' => $calculationData['wallet'] ? $calculationData['wallet']->currency->name : 'Default',
            '[[txn_id]]' => $transaction->tnx,
            '[[date]]' => $transaction->created_at,
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $agent->email,
            'agent_cashout_received',
            'Agent',
            $shortcodes,
            $agent->phone,
            $agent->id,
            ''
        );
    }

    public function validateCashout(Request $request)
    {

        if (! setting('user_cashout', 'permission')) {
            return makeValidationException([
                'cashout' => [__('Cashout feature is not enabled!')],
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
            'agent_number' => 'required|exists:users,account_number',
            'amount' => 'required|numeric',
            'wallet_id' => setting('multiple_currency', 'permission') ? 'required' : 'nullable',
        ], [
            'agent_number.exists' => __('Agent account number does not exist'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        return true;
    }

    public function getCashoutHistory($request)
    {
        $user = auth()->user();

        $query = Transaction::query()
            ->with(['userWallet.currency', 'fromUser'])
            ->where('user_id', $user->id)
            ->where('type', TxnType::CashOut);

        $query->when($request->tnx_id, fn ($q) => $q->where('tnx', $request->tnx_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        return $query->latest();
    }
}
