<?php

namespace App\Http\Controllers\Api\Agent;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Services\TransactionReport;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatisticsController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public TransactionReport $report, public $user = null)
    {
        $this->user = request()->user();
    }

    public function circleCharts()
    {
        try {
            $user = $this->user;
            $date = request()->date ?? 'seven_days';

            $transactions = $this->report->getChartCircleActivity($user, $date);

            $data = [
                'labels' => [__('Deposit'), __('Withdraw'), __('Cash In'), __('Cash Out')],
                'series' => $transactions,
                'totalMoneyReceived' => $transactions,
            ];

            return $this->success($data, __('Circle chart data'));

        } catch (\Throwable $throwable) {
            return $this->error($throwable->getMessage());
        }
    }

    public function activityChartInfo(Request $request)
    {
        try {
            $user = $this->user;
            $startDate = $request->start_date ? Carbon::createFromDate($request->start_date) : Carbon::now()->subDays(6);
            $endDate = $request->end_date ? Carbon::createFromDate($request->end_date) : Carbon::now();
            $wallet_id = $request->get('wallet_id', 'default');

            $dateArray = generate_date_range_array($startDate, $endDate);

            $transactions = $this->report->getTransactionsByDateRange($user->id, $startDate, $endDate, $wallet_id, [
                TxnType::Deposit,
                TxnType::ManualDeposit,
                TxnType::Withdraw,
                TxnType::WithdrawAuto,
                TxnType::CashIn,
                TxnType::CashReceived,
            ]);

            $data = $this->report->prepareChartDataByDateRange($transactions, $dateArray);

            $maxMinValues = $this->report->calculateMaxMinValues($data);

            return $this->success([
                'deposit' => $data['deposit'],
                'withdraw' => $data['withdraw'],
                'cashin' => $data['cashin'],
                'cashout' => $data['cash_received'],
                'currentMonth' => array_keys($data['deposit']),
                'maxValue' => round($maxMinValues['max']) + 50,
                'minValue' => $maxMinValues['min'],
            ], __('Activity chart data'));

        } catch (\Throwable $throwable) {
            return $this->error($throwable->getMessage());
        }
    }
}
