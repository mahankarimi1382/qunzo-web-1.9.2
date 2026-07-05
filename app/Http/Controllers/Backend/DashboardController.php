<?php

namespace App\Http\Controllers\Backend;

use App\Enums\AgentStatus;
use App\Enums\MerchantStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Currency;
use App\Models\Gateway;
use App\Models\LoginActivities;
use App\Models\Merchant;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = User::query();
        $totalStaff = Admin::count();
        $latestUser = User::latest()->take(5)->where('role', UserType::User)->get();
        $totalGateway = Gateway::where('status', true)->count();

        // Start/end date setup
        $startDate = request()->start_date ? Carbon::createFromDate(request()->start_date) : Carbon::now()->subDays(7);
        $endDate = request()->end_date ? Carbon::createFromDate(request()->end_date) : Carbon::now();
        $dateArray = array_fill_keys(generate_date_range_array($startDate, $endDate), 0);
        $dateFilter = [$startDate->startOfDay(), $endDate->endOfDay()];
        $orderedDates = array_keys($dateArray);

        $currency = request()->get('currency', 'default');

        // Statistic formatter
        $formatStats = function ($stats) use ($orderedDates) {
            return collect($orderedDates)->mapWithKeys(function ($date) use ($stats) {
                return [$date => $stats[$date] ?? 0];
            })->toArray();
        };

        // Helper function
        $getStatistics = function ($type) use ($dateFilter, $currency, $dateArray) {
            $query = Transaction::whereBetween('created_at', $dateFilter)
                ->where('type', $type)
                ->where('status', TxnStatus::Success)
                ->currency($currency)
                ->get()
                ->groupBy(fn ($item) => now()->parse($item->created_at)->format('d M'))
                ->map(fn ($group) => $group->sum('amount'))
                ->toArray();

            return array_merge($dateArray, $query);
        };

        $getMultipleStatistics = function (array $types) use ($dateFilter, $currency, $dateArray) {
            $query = Transaction::whereBetween('created_at', $dateFilter)
                ->whereIn('type', $types)
                ->where('status', TxnStatus::Success)
                ->currency($currency)
                ->get()
                ->groupBy(fn ($item) => now()->parse($item->created_at)->format('d M'))
                ->map(fn ($group) => $group->sum('amount'))
                ->toArray();

            return array_merge($dateArray, $query);
        };

        // Get raw stats
        $rawDeposit = $getMultipleStatistics([TxnType::Deposit, TxnType::ManualDeposit]);
        $rawWithdraw = $getMultipleStatistics([TxnType::Withdraw, TxnType::WithdrawAuto]);
        $rawCashout = $getStatistics(TxnType::CashOut);
        $rawPayment = $getStatistics(TxnType::Payment);
        $rawTransfer = $getStatistics(TxnType::SendMoney);

        // Format them to date order
        $totalDeposit = $formatStats($rawDeposit);
        $totalWithdraw = $formatStats($rawWithdraw);
        $cashoutStatistics = $formatStats($rawCashout);
        $paymentStatistics = $formatStats($rawPayment);
        $transferStatistics = $formatStats($rawTransfer);

        $loginActivities = Cache::remember('login-activities', 60, fn () => LoginActivities::get());

        $browser = $loginActivities->groupBy('browser')->map->count()->toArray();
        $platform = $loginActivities->groupBy('platform')->map->count()->toArray();

        $country = User::all()->groupBy('country')->map->count()->toArray();
        arsort($country);
        $country = array_slice($country, 0, 5);

        $symbol = $currency === 'default' ? setting('currency_symbol', 'global') : Currency::query()->findOrFail($currency)?->symbol;

        $currencies = Currency::query()->where('status', true)->get();

        $data = [
            'register_user' => $user->where('role', UserType::User)->count(),
            'all_deposits' => Transaction::whereIn('type', [TxnType::Deposit, TxnType::ManualDeposit])->where('status', TxnStatus::Success)->count(),
            'all_currencies' => Currency::count(),
            'latest_user' => $latestUser,
            'currencies' => $currencies,
            'latest_merchants' => Merchant::where('status', MerchantStatus::Approved)->latest()->take(5)->get(),
            'latest_agents' => Agent::where('status', AgentStatus::Approved)->latest()->take(5)->get(),
            'total_staff' => $totalStaff,
            'total_merchants' => User::where('role', UserType::Merchant)->count(),
            'total_agents' => User::where('role', UserType::Agent)->count(),
            'total_payments' => Transaction::where('type', TxnType::Payment)->where('status', TxnStatus::Success)->count(),
            'total_withdraw' => Transaction::whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])->where('status', TxnStatus::Success)->count(),
            'total_cashout' => Transaction::where('type', TxnType::CashOut)->where('status', TxnStatus::Success)->count(),
            'total_transfer' => Transaction::where('type', TxnType::SendMoney)->where('status', TxnStatus::Success)->count(),
            'date_label' => array_fill_keys($orderedDates, 0),
            'withdraw_statistics' => $totalWithdraw,
            'deposit_statistics' => $totalDeposit,
            'cashout_statistics' => $cashoutStatistics,
            'payment_statistics' => $paymentStatistics,
            'transfer_statistics' => $transferStatistics,
            'start_date' => request()->has('start_date') && request()->start_date !== null ? $startDate->format('m/d/Y') : $startDate->format('m/d/Y'),
            'end_date' => request()->has('end_date') && request()->end_date !== null ? $endDate->format('m/d/Y') : $endDate->format('m/d/Y'),
            'total_gateway' => $totalGateway,
            'total_ticket' => Ticket::count(),
            'browser' => $browser,
            'platform' => $platform,
            'country' => $country,
            'symbol' => $symbol,
        ];

        if (request()->ajax()) {
            return response()->json([
                'date_label' => $data['date_label'],
                'withdraw_statistics' => $data['withdraw_statistics'],
                'deposit_statistics' => $data['deposit_statistics'],
                'cashout_statistics' => $data['cashout_statistics'],
                'payment_statistics' => $data['payment_statistics'],
                'transfer_statistics' => $data['transfer_statistics'],
                'symbol' => $symbol,
            ]);
        }

        return view('backend.dashboard', ['data' => $data]);
    }
}
