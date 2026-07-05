<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Enums\UserType;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CashInService
{
    use NotifyTrait;

    public function cashIn($request)
    {
        $agent = auth()->user();
        $isApi = $request->expectsJson();

        try {

            $user = $this->findUser($request->account_number, $agent->id);
            if (isValidationException($user)) {
                return $user;
            }

            $calculationData = $this->calculateCashInAmounts($request);
            if (isValidationException($calculationData)) {
                return $calculationData;
            }

            $limitCheck = $this->validateLimits($agent, $calculationData);
            if (isValidationException($limitCheck)) {
                return $limitCheck;
            }

            $balanceValidation = $this->validateAgentBalance($agent, $calculationData);
            if (isValidationException($balanceValidation)) {
                return $balanceValidation;
            }

            DB::beginTransaction();

            $cashInResult = $this->processCashIn($agent, $user, $calculationData, $request);

            $this->processAgentCommission($calculationData['amount'], $agent, $user, $calculationData);

            $this->processReferralBonus($user, $calculationData);

            DB::commit();

            return $cashInResult;
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

    public function findUser($accountNumber, $currentAgentId)
    {
        $user = User::where('account_number', $accountNumber)
            ->where('role', UserType::User)
            ->first();

        if (! $user) {
            return makeValidationException([
                'account_number' => [__('User not found!')],
            ]);
        }

        if (! $user->status) {
            return makeValidationException([
                'account_number' => [__('User account is inactive!')],
            ]);
        }

        if ($user->id === $currentAgentId) {
            return makeValidationException([
                'account_number' => [__('You cannot cash in to your own account!')],
            ]);
        }

        return $user;
    }

    public function calculateCashInAmounts($request)
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
                    'wallet_id' => [__('Wallet not found!')],
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
        $cashInCharge = setting('cashin_charge', 'cashin');
        $cashInChargeType = setting('cashin_charge_type', 'cashin');

        if ($cashInChargeType === 'percentage') {
            return ($cashInCharge * $amount) / 100;
        } else {
            return $wallet
                ? $cashInCharge * $wallet->currency->conversion_rate
                : $cashInCharge;
        }
    }

    public function validateAmountLimits($amount, $wallet)
    {
        $currency = $wallet ? $wallet->currency->code : setting('site_currency', 'global');

        $minAmount = CurrencyService::getConvertedAmount(
            setting('cashin_minimum', 'cashin'),
            $currency,
            true
        );

        $maxAmount = CurrencyService::getConvertedAmount(
            setting('cashin_maximum', 'cashin'),
            $currency,
            true
        );

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

    public function validateLimits($agent, $calculationData)
    {
        $wallet = $calculationData['wallet'];
        $currency = $calculationData['currency'];
        $walletType = $wallet ? $wallet->id : 'default';

        $todayTransactions = Transaction::query()
            ->where('user_id', $agent->id)
            ->where('type', TxnType::CashIn)
            ->where('wallet_type', $walletType)
            ->whereDate('created_at', now())
            ->sum('amount');

        $dailyLimit = CurrencyService::getConvertedAmount(
            setting('cashin_daily_limit', 'cashin'),
            $currency,
            true
        );

        if (($todayTransactions + $calculationData['amount']) > $dailyLimit) {
            return makeValidationException([
                'amount' => [__('Today cash in limit has been reached!')],
            ]);
        }

        $monthlyTransactions = Transaction::query()
            ->where('user_id', $agent->id)
            ->where('type', TxnType::CashIn)
            ->where('wallet_type', $walletType)
            ->whereMonth('created_at', now())
            ->sum('amount');

        $monthlyLimit = CurrencyService::getConvertedAmount(
            setting('cashin_monthly_limit', 'cashin'),
            $currency,
            true
        );

        if (($monthlyTransactions + $calculationData['amount']) > $monthlyLimit) {
            return makeValidationException([
                'amount' => [__('This month cash in limit has been reached!')],
            ]);
        }
    }

    public function validateAgentBalance($agent, $calculationData)
    {
        if ($calculationData['wallet']) {
            if ($calculationData['wallet']->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [__('Insufficient funds!')],
                ]);
            }
        } else {
            if ($agent->balance < $calculationData['final_amount']) {
                return makeValidationException([
                    'amount' => [__('Insufficient funds!')],
                ]);
            }
        }
    }

    public function processCashIn($agent, $user, $calculationData, $request)
    {
        $walletId = $calculationData['wallet'] ? $calculationData['wallet']->id : 'default';

        if ($calculationData['wallet']) {
            $agentWallet = UserWallet::where([
                'user_id' => $agent->id,
                'currency_id' => $calculationData['wallet']->currency_id,
            ])->first();
            $agentWallet->decrement('balance', $calculationData['final_amount']);
        } else {
            $agent->decrement('balance', $calculationData['final_amount']);
        }

        if ($calculationData['wallet']) {
            $userWallet = UserWallet::firstOrCreate([
                'user_id' => $user->id,
                'currency_id' => $calculationData['wallet']->currency_id,
            ], [
                'balance' => 0,
            ]);
            $userWallet->increment('balance', $calculationData['amount']);
        } else {
            $user->increment('balance', $calculationData['amount']);
        }

        $agentTransaction = Transaction::create([
            'user_id' => $agent->id,
            'description' => 'Cash In',
            'target_id' => $user->id,
            'type' => TxnType::CashIn,
            'amount' => $calculationData['amount'],
            'wallet_type' => $walletId,
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['final_amount'],
            'method' => 'Agent',
            'status' => TxnStatus::Success,
        ]);

        $userTransaction = Transaction::create([
            'user_id' => $user->id,
            'description' => 'Cash In From '.$agent->full_name,
            'from_user_id' => $agent->id,
            'type' => TxnType::CashIn,
            'amount' => $calculationData['amount'],
            'wallet_type' => $walletId,
            'charge' => 0,
            'final_amount' => $calculationData['amount'],
            'method' => 'Agent',
            'status' => TxnStatus::Success,
        ]);

        $this->sendUserNotification($user, $agent, $calculationData, $userTransaction);

        return [
            'agent_transaction' => $agentTransaction,
            'user_transaction' => $userTransaction,
        ];
    }

    public function processReferralBonus($user, $calculationData)
    {
        if (setting('cashin', 'referral_level')) {
            $level = LevelReferral::where('type', 'cashin')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus(
                $user,
                'cashin',
                $calculationData['amount'],
                $level,
                1,
                null,
                $calculationData['wallet']
            );
        }
    }

    public function processAgentCommission($cashInAmount, $agent, $user, $calculationData)
    {
        $commission = 0;
        $cashInAgentCommission = setting('cashin_agent_commission', 'cashin');
        $cashInAgentCommissionType = setting('cashin_agent_commission_type', 'cashin');

        if ($cashInAgentCommissionType === 'percentage') {
            $commission = ($cashInAgentCommission * $cashInAmount) / 100;
        } else {
            $commission = $calculationData['wallet']
                ? $cashInAgentCommission * $calculationData['wallet']->currency->conversion_rate
                : $cashInAgentCommission;
        }

        $agentCommissionTxn = Transaction::create([
            'user_id' => $agent->id,
            'description' => 'Cash In Commission',
            'type' => TxnType::CashInCommission,
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

    private function sendUserNotification($user, $agent, $calculationData, $transaction)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[amount]]' => formatAmount($calculationData['amount'], $calculationData['currency']),
            '[[charge]]' => formatAmount(0, $calculationData['currency']),
            '[[total_amount]]' => formatAmount($calculationData['amount'], $calculationData['currency']),
            '[[wallet]]' => $calculationData['wallet'] ? $calculationData['wallet']->currency->name : 'Default',
            '[[agent_name]]' => $agent->full_name,
            '[[agent_account_no]]' => $agent->account_number,
            '[[transaction_link]]' => Route::has('user.transactions') ? route('user.transactions') : '',
            '[[site_title]]' => setting('site_title', 'global'),
            '[[currency]]' => $calculationData['currency'],
            '[[txn_id]]' => $transaction->tnx,
        ];

        $this->sendNotify(
            $user->email,
            'user_cash_in',
            'User',
            $shortcodes,
            $user->phone,
            $user->id,
            Route::has('user.transactions') ? route('user.transactions') : ''
        );
    }

    private function sendAgentNotification($agent, $commission, $calculationData, $transaction)
    {
        $shortcodes = [
            '[[full_name]]' => $agent->full_name,
            '[[amount]]' => formatAmount($commission, $calculationData['currency']),
            '[[wallet]]' => $calculationData['wallet'] ? $calculationData['wallet']->currency->name : 'Default',
            '[[commission_at]]' => now(),
            '[[txn_id]]' => $transaction->tnx,
            '[[transaction_link]]' => Route::has('agent.profit.history') ? route('agent.profit.history') : '',
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $agent->email,
            'agent_commission',
            'Agent',
            $shortcodes,
            $agent->phone,
            $agent->id,
            Route::has('agent.profit.history') ? route('agent.profit.history') : ''
        );
    }

    public function validateCashIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|exists:users,account_number',
            'amount' => 'required|numeric',
            'wallet_id' => setting('multiple_currency', 'permission') ? 'required' : 'nullable',
        ], [
            'account_number.exists' => __('User account number does not exist'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        // check payment status
        if (! auth()->user()->payment_status) {
            return makeValidationException([
                'payment_status' => [__('Cash In is disabled for you')],
            ]);
        }

        // validate wallet if not default
        if ($request->wallet_id && $request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', auth()->id())
                ->where('id', $request->wallet_id)
                ->exists();

            if (! $wallet) {
                return makeValidationException([
                    'wallet_id' => [__('Wallet not found')],
                ]);
            }
        }

        return true;
    }

    public function getCashInHistory($request)
    {
        $agent = auth()->user();

        $query = Transaction::query()
            ->with(['userWallet.currency'])

            ->when($request->type == 'cash_out', function ($query) use ($agent) {
                $query->where('type', TxnType::CashOut)->where('from_user_id', $agent->id);
            }, function ($query) use ($agent) {
                $query->where('type', TxnType::CashIn)->where('user_id', $agent->id);
            });

        $query->when($request->tnx_id, fn ($q) => $q->where('tnx', $request->tnx_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        return $query->latest();
    }
}
