<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionReport
{
    public function getDaysInMonth($year, $month)
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        return range(1, $daysInMonth);
    }

    public function getTransactions($userId, $month, $year, $walletId = null, $types = null)
    {
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', $types ?? TxnType::cases())
            ->when($walletId, function ($query, $value) {
                $query->where('wallet_type', $value);
            })->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);

        return $query->select([
            'type',
            DB::raw('DATE(created_at) as date'),
            DB::raw('EXTRACT(DAY FROM created_at) as everyday'),
            DB::raw('SUM(amount) as total_amount'),
        ])
            ->groupBy('type', 'date', 'everyday')
            ->orderBy('date')
            ->get();
    }

    public function getTransactionsByDateRange($userId, $startDate, $endDate, $walletId = null, $types = null)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', $types ?? TxnType::cases())
            ->where('status', TxnStatus::Success)
            ->when($walletId, function ($query, $value) {
                $query->where('wallet_type', $value);
            })
            ->whereBetween('created_at', [$startDate, $endDate]);

        return $query->select([
            'type',
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total_amount'),
        ])
            ->groupBy('type', 'date')
            ->orderBy('date')
            ->get();
    }

    public function prepareChartData($transactions, $daysInMonth)
    {
        $deposit = array_fill(0, count($daysInMonth), 0);
        $withdraw = array_fill(0, count($daysInMonth), 0);
        $transfer = array_fill(0, count($daysInMonth), 0);
        $cashin = array_fill(0, count($daysInMonth), 0);
        $cashout = array_fill(0, count($daysInMonth), 0);
        $gift = array_fill(0, count($daysInMonth), 0);
        $invoice = array_fill(0, count($daysInMonth), 0);
        $requestMoney = array_fill(0, count($daysInMonth), 0);
        $payments = array_fill(0, count($daysInMonth), 0);

        foreach ($transactions as $transaction) {
            $dayIndex = $transaction->everyday - 1;

            match ($transaction->type) {
                TxnType::Deposit,
                TxnType::ManualDeposit => $deposit[$dayIndex] += $transaction->total_amount,

                TxnType::Payment => $payments[$dayIndex] += $transaction->total_amount,

                TxnType::Withdraw,
                TxnType::WithdrawAuto => $withdraw[$dayIndex] += $transaction->total_amount,

                TxnType::SendMoney => $transfer[$dayIndex] += $transaction->total_amount,

                TxnType::CashIn => $cashin[$dayIndex] += $transaction->total_amount,

                TxnType::CashOut => $cashout[$dayIndex] += $transaction->total_amount,
                TxnType::CashReceived => $cashout[$dayIndex] += $transaction->total_amount,

                TxnType::GiftRedeemed => $gift[$dayIndex] += $transaction->total_amount,

                TxnType::Invoice => $invoice[$dayIndex] += $transaction->total_amount,

                TxnType::RequestMoney => $requestMoney[$dayIndex] += $transaction->total_amount,

                default => null,
            };
        }

        return [
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'transfer' => $transfer,
            'cashin' => $cashin,
            'cashout' => $cashout,
            'cash_received' => $cashout,
            'gift' => $gift,
            'invoice' => $invoice,
            'request_money' => $requestMoney,
            'payments' => $payments,
        ];
    }

    public function prepareChartDataByDateRange($transactions, $dateArray)
    {
        $deposit = array_fill_keys($dateArray, 0);
        $withdraw = array_fill_keys($dateArray, 0);
        $transfer = array_fill_keys($dateArray, 0);
        $cashin = array_fill_keys($dateArray, 0);
        $cashout = array_fill_keys($dateArray, 0);
        $gift = array_fill_keys($dateArray, 0);
        $invoice = array_fill_keys($dateArray, 0);
        $requestMoney = array_fill_keys($dateArray, 0);
        $payments = array_fill_keys($dateArray, 0);

        foreach ($transactions as $transaction) {
            $dateKey = Carbon::parse($transaction->date)->format('d M');

            match ($transaction->type) {
                TxnType::Deposit,
                TxnType::ManualDeposit => $deposit[$dateKey] = ($deposit[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::Payment => $payments[$dateKey] = ($payments[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::Withdraw,
                TxnType::WithdrawAuto => $withdraw[$dateKey] = ($withdraw[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::SendMoney => $transfer[$dateKey] = ($transfer[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::CashIn => $cashin[$dateKey] = ($cashin[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::CashOut => $cashout[$dateKey] = ($cashout[$dateKey] ?? 0) + $transaction->total_amount,
                TxnType::CashReceived => $cashout[$dateKey] = ($cashout[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::GiftRedeemed => $gift[$dateKey] = ($gift[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::Invoice => $invoice[$dateKey] = ($invoice[$dateKey] ?? 0) + $transaction->total_amount,

                TxnType::RequestMoney => $requestMoney[$dateKey] = ($requestMoney[$dateKey] ?? 0) + $transaction->total_amount,

                default => null,
            };
        }

        return [
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'transfer' => $transfer,
            'cashin' => $cashin,
            'cashout' => $cashout,
            'cash_received' => $cashout,
            'gift' => $gift,
            'invoice' => $invoice,
            'request_money' => $requestMoney,
            'payments' => $payments,
        ];
    }

    public function calculateMaxMinValues($data)
    {
        $allValues = [
            max($data['deposit']),
            min($data['deposit']),
            max($data['withdraw']),
            min($data['withdraw']),
            max($data['transfer']),
            min($data['transfer']),
            max($data['payments']),
            min($data['payments']),
            max($data['cashout']),
            min($data['cashout']),
            max($data['gift']),
            min($data['gift']),
            max($data['invoice']),
            min($data['invoice']),
            max($data['request_money']),
            min($data['request_money']),
        ];

        return [
            'max' => max($allValues),
            'min' => min($allValues),
        ];
    }

    public function getChartCircleActivity($user, $date)
    {
        $query = Transaction::query()
            ->where('user_id', $user->id);

        $dateRange = $this->activityChartDateRange($date);

        $transactions = $query->whereBetween('created_at', $dateRange);

        $depositTransactions = (clone $transactions)
            ->whereIn('type', [TxnType::ManualDeposit, TxnType::Deposit])
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $withdrawTransactions = (clone $transactions)
            ->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $cashInTransactions = (clone $transactions)
            ->where('type', TxnType::CashIn)
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $cashReceivedTransactions = (clone $transactions)
            ->where('type', TxnType::CashReceived)
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $defaultCurrency = setting('site_currency', 'global');

        return [
            (float) formatAmount($depositTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($withdrawTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($cashInTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($cashReceivedTransactions, $defaultCurrency, thousandSeparatorRemove: true),
        ];
    }

    public function getChartCircleActivityByDateRange($user, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $depositTransactions = (clone $query)
            ->whereIn('type', [TxnType::ManualDeposit, TxnType::Deposit])
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $withdrawTransactions = (clone $query)
            ->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $cashInTransactions = (clone $query)
            ->where('type', TxnType::CashIn)
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $cashReceivedTransactions = (clone $query)
            ->where('type', TxnType::CashReceived)
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $defaultCurrency = setting('site_currency', 'global');

        return [
            (float) formatAmount($depositTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($withdrawTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($cashInTransactions, $defaultCurrency, thousandSeparatorRemove: true),
            (float) formatAmount($cashReceivedTransactions, $defaultCurrency, thousandSeparatorRemove: true),
        ];
    }

    public function getFilledChartActivity($user, $date)
    {
        $query = Transaction::query()
            ->where('user_id', $user->id);

        $dateRange = $this->activityChartDateRange($date);

        $transactions = $query->whereBetween('created_at', $dateRange);

        $successAmount = (clone $transactions)
            ->where('status', TxnStatus::Success)
            ->sum('amount');

        $pendingAmount = (clone $transactions)
            ->where('status', TxnStatus::Pending)
            ->sum('amount');

        $defaultCurrency = setting('site_currency', 'global');

        $successFormatted = (float) formatAmount($successAmount, $defaultCurrency, thousandSeparatorRemove: true);
        $pendingFormatted = (float) formatAmount($pendingAmount, $defaultCurrency, thousandSeparatorRemove: true);

        $total = $successFormatted + $pendingFormatted;

        $successPercent = $total > 0 ? round(($successFormatted / $total) * 100, 2) : 0;
        $pendingPercent = $total > 0 ? round(($pendingFormatted / $total) * 100, 2) : 0;

        return [
            $successPercent,
            $pendingPercent,
        ];
    }

    private function activityChartDateRange($date)
    {
        switch ($date) {
            case 'seven_days':
                $startDate = now()->subDays(6)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'thirty_days':
                $startDate = now()->subDays(29)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $endDate = now()->subYear()->endOfYear();
                break;
            default:
                throw new \Exception('Invalid date');
        }

        return [$startDate, $endDate];
    }
}
