<?php

namespace App\Services\User;

use App\Enums\CurrencyStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Enums\UserType;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Services\TransactionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardService
{
    protected TransactionReport $report;

    public function __construct(TransactionReport $report)
    {
        $this->report = $report;
    }

    public function dashboard(Request $request, $isApi = false)
    {
        $data = [];

        $user = $request->user();

        $transactions = Transaction::with('userWallet.currency')->when($isApi, function ($query) {
            $query->select([
                'id',
                'tnx',
                'description',
                'type',
                'amount',
                'charge',
                'final_amount',
                'pay_currency',
                'pay_currency',
                'status',
                'created_at',
                'wallet_type',
            ]);
        })
            ->where('user_id', $user->id)
            ->when($request->filled('txn'), function ($query) use ($request) {
                $query->where('tnx', 'like', '%'.$request->input('txn').'%');
            })
            ->when($request->filled('status') && $request->input('status') != 'All', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $wallets = UserWallet::query()
            ->with('currency')
            ->where('user_id', $user->id)
            ->oldest();

        $userWallets = $wallets->get();

        $last_login = auth()->user()->activities->last();
        $browser = getBrowser($last_login?->agent);

        if (! $isApi) {
            $currencies = Currency::query()
                ->where('status', CurrencyStatus::Active)
                ->whereNotIn('id', $wallets->pluck('currency_id'))
                ->get();
            $data['earnings'] = [
                'amount' => [],
                'months' => [],
            ];

            $statistics['browser'] = data_get($browser, 'platform').' . '.data_get($browser, 'browser');
            $statistics['referral_code'] = $user->referral_code;

            $statistics['last_login'] = $last_login?->created_at->format('d M, h:i A');
            $statistics['total_wallets'] = $userWallets->count() + 1;
            $statistics['total_deposit'] = Transaction::where('user_id', $user->id)->whereIn('type', [TxnType::Deposit, TxnType::ManualDeposit])->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['total_withdraw'] = Transaction::where('user_id', $user->id)->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['total_payments'] = Transaction::where('user_id', $user->id)->where('type', TxnType::Payment)->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['total_cashout'] = Transaction::where('user_id', $user->id)->where('type', TxnType::CashOut)->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['total_transfer'] = Transaction::where('user_id', $user->id)->where('type', TxnType::SendMoney)->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['referral_bonuses'] = Transaction::where('user_id', $user->id)->where('type', TxnType::Referral)->where('wallet_type', 'default')->where('status', TxnStatus::Success)->sum('amount');
            $statistics['total_tickets'] = $user->tickets()->count();
            $statistics['total_referral'] = $user->referrals()->count();
            $statistics['total_transactions'] = $user->transaction()->count();
        } else {
            foreach (TxnStatus::cases() as $status) {
                $txtStatus[$status->value] = str($status->value)->headline();
            }
            $referral['bonus'] = setting('currency_symbol', 'global').setting('referral_bonus', 'fee');
            $referral['count'] = $user->referrals()->count();
            $referral['referral_code'] = $user->referral_code;

            $statistics['browser'] = data_get($browser, 'platform').' . '.data_get($browser, 'browser');

            // time wise wish
            $currentHour = Carbon::now()->hour;

            $info['time_wise_wish'] = greeting();
            $info['exchange_config'] = [
                'charge' => setting('exchange_charge', 'fee'),
                'charge_type' => setting('exchange_charge_type', 'fee'),
            ];

            $info['last_login'] = $last_login?->created_at->format('d M, h:i A');

            $info['unread_notifications_count'] = $user->notifications()
                ->where('for', 'user')
                ->where('read', 0)->count();

            return ['referral' => $referral, 'info' => $info, 'user' => $user->only(['full_name', 'username', 'account_number', 'email', 'avatar_path'])];
        }

        return ['info' => $data, 'statistics' => $statistics, 'transactions' => $transactions, 'userWallets' => $userWallets, 'currencies' => $currencies, 'user' => $user];
    }

    public function statistics(Request $request)
    {
        $user = $request->user();

        $userWallets = UserWallet::query()
            ->with('currency')
            ->where('user_id', $user->id)
            ->oldest();

        $statistics = [
            'total_deposits' => Transaction::where('user_id', $user->id)
                ->whereIn('type', [TxnType::Deposit, TxnType::ManualDeposit])
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'total_wallets' => $userWallets->count() + 1,

            'total_cashouts' => Transaction::where('user_id', $user->id)
                ->where('type', TxnType::CashOut)
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'total_withdraws' => Transaction::where('user_id', $user->id)
                ->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'total_payments' => Transaction::where('user_id', $user->id)
                ->where('type', TxnType::Payment)
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'total_transfers' => Transaction::where('user_id', $user->id)
                ->where('type', TxnType::SendMoney)
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'referral_bonus' => Transaction::where('user_id', $user->id)
                ->where('type', TxnType::Referral)
                ->where('wallet_type', 'default')
                ->where('status', TxnStatus::Success)
                ->sum('amount'),

            'total_referrals' => $user->referrals()->count(),
            'total_transactions' => $user->transaction()->count(),
            'total_tickets' => $user->tickets()->count(),
        ];

        $currencySymbol = setting('currency_symbol', 'global');
        $currencyCode = setting('site_currency', 'global');

        return [
            ['name' => 'Total Deposits',     'type' => 'total-deposits',     'value' => $currencySymbol.formatAmount($statistics['total_deposits'], $currencyCode)],
            ['name' => 'Total Wallets',      'type' => 'total-wallets',      'value' => (string) $statistics['total_wallets']],
            ['name' => 'Total Cashouts',     'type' => 'total-cashouts',     'value' => $currencySymbol.formatAmount($statistics['total_cashouts'], $currencyCode)],
            ['name' => 'Total Withdraws',    'type' => 'total-withdraws',    'value' => $currencySymbol.formatAmount($statistics['total_withdraws'], $currencyCode)],
            ['name' => 'Total Payments',     'type' => 'total-payments',     'value' => $currencySymbol.formatAmount($statistics['total_payments'], $currencyCode)],
            ['name' => 'Total Transfer',     'type' => 'total-transfers',    'value' => $currencySymbol.formatAmount($statistics['total_transfers'], $currencyCode)],
            ['name' => 'Referral Bonus',     'type' => 'referral-bonus',     'value' => $currencySymbol.formatAmount($statistics['referral_bonus'], $currencyCode)],
            ['name' => 'Total Referrals',    'type' => 'total-referrals',    'value' => (string) $statistics['total_referrals']],
            ['name' => 'Total Transactions', 'type' => 'total-transactions', 'value' => (string) $statistics['total_transactions']],
            ['name' => 'Total Tickets',      'type' => 'total-tickets',      'value' => (string) $statistics['total_tickets']],
        ];
    }

    public function qrCode($user)
    {
        $code = match (true) {
            $user->role === UserType::User => 'UID: '.$user->account_number,
            $user->role === UserType::Merchant => 'MID: '.$user->account_number,
            $user->role === UserType::Agent => 'AID: '.$user->account_number,
        };

        return QrCode::size(184)->generate($code);
    }

    public function getRangeDate($selectedMonth, $selectedYear)
    {
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $dates = collect();
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dates->push($current->format('j M'));
            $current->addDay();
        }

        return $dates;
    }

    public function activityChart(Request $request)
    {
        try {
            $user = $request->user();
            $startDate = $request->start_date ? Carbon::createFromDate($request->start_date) : Carbon::now()->subDays(6);
            $endDate = $request->end_date ? Carbon::createFromDate($request->end_date) : Carbon::now();
            $wallet_id = $request->get('wallet_id', 'default');

            $dateArray = generate_date_range_array($startDate, $endDate);

            $transactions = $this->report->getTransactionsByDateRange($user->id, $startDate, $endDate, $wallet_id, [TxnType::Deposit, TxnType::ManualDeposit, TxnType::Withdraw, TxnType::WithdrawAuto, TxnType::SendMoney]);

            $data = $this->report->prepareChartDataByDateRange($transactions, $dateArray);

            $maxMinValues = $this->report->calculateMaxMinValues($data);

            return [
                'success' => true,
                'data' => [
                    'deposit' => $data['deposit'],
                    'withdraw' => $data['withdraw'],
                    'transfer' => $data['transfer'],
                    'currentMonth' => array_keys($data['deposit']),
                    'maxValue' => round($maxMinValues['max']) + 50,
                    'minValue' => $maxMinValues['min'],
                ],
            ];
        } catch (\Throwable $throwable) {
            return [
                'success' => false,
                'message' => $throwable->getMessage(),
            ];
        }
    }

    public function merchantActivityChart(Request $request)
    {
        try {
            $user = $request->user();
            $startDate = $request->start_date ? Carbon::createFromDate($request->start_date) : Carbon::now()->subDays(7);
            $endDate = $request->end_date ? Carbon::createFromDate($request->end_date) : Carbon::now();
            $wallet_id = $request->get('wallet_id', 'default');

            $dateArray = generate_date_range_array($startDate, $endDate);

            $transactions = $this->report->getTransactionsByDateRange($user->id, $startDate, $endDate, $wallet_id, [TxnType::Withdraw, TxnType::WithdrawAuto, TxnType::Payment]);

            $data = $this->report->prepareChartDataByDateRange($transactions, $dateArray);
            $maxMinValues = $this->report->calculateMaxMinValues($data);

            return [
                'success' => true,
                'data' => [
                    'withdraw' => $data['withdraw'],
                    'payment' => $data['payments'],
                    'currentMonth' => array_keys($data['withdraw']),
                    'maxValue' => round($maxMinValues['max']) + 50,
                    'minValue' => $maxMinValues['min'],
                ],
            ];
        } catch (\Throwable $throwable) {
            return [
                'success' => false,
                'message' => $throwable->getMessage(),
            ];
        }
    }

    public function circleChart(Request $request)
    {
        try {
            $user = $request->user();

            $transactions = $this->report->getFilledChartActivity($user, $request->date);

            $data = [
                'labels' => [
                    'success',
                    'pending',
                ],
                'series' => $transactions,
            ];

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Throwable $throwable) {
            return [
                'success' => false,
                'message' => $throwable->getMessage(),
            ];
        }
    }
}
