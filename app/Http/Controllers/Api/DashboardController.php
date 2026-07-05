<?php

namespace App\Http\Controllers\Api;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\User\DashboardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function dashboard()
    {
        return $this->success(app(DashboardService::class)->dashboard(request(), true), __('Dashboard data'));
    }

    public function statistics()
    {
        return $this->success(app(DashboardService::class)->statistics(request(), true), __('Statistics data'));
    }

    public function qrCode()
    {
        return $this->success(app(DashboardService::class)->qrCode(request()->user())->toHtml(), __('QR Code'));
    }

    public function activityChartInfo(Request $request)
    {
        $response = app(DashboardService::class)->activityChart($request);
        if ($response['success']) {
            return $this->success($response['data'], __('Activity chart data'));
        }

        return $this->error($response['message']);
    }

    public function merchantActivityChartInfo(Request $request)
    {
        $response = app(DashboardService::class)->merchantActivityChart($request);
        if ($response['success']) {
            return $this->success($response['data'], __('Merchant activity chart data'));
        }

        return $this->error($response['message']);
    }

    public function circleChartInfo(Request $request)
    {
        $response = app(DashboardService::class)->circleChart($request, true);
        if ($response['success']) {
            return $this->success($response, __('Circle chart data'));
        }

        return $this->error($response['message']);
    }

    public function transactionDetail($tnx)
    {
        $user = request()->user();

        $transaction = Transaction::with('userWallet.currency', 'user')->where('user_id', $user->id)->where('tnx', $tnx)->first();
        if (! $transaction) {
            return $this->error(__('Transaction not found'), 404);
        }

        return $this->success(TransactionResource::make($transaction), __('Transaction detail'));
    }

    public function transactions(Request $request, $tnx = null)
    {
        $user = request()->user();

        $transactions = Transaction::with('userWallet.currency', 'user')->where('user_id', $user->id)
            ->when($request->filled('txn'), function ($query) use ($request) {
                $query->where('tnx', 'like', '%'.$request->input('txn').'%');
            })
            ->when($tnx, function ($query) use ($tnx) {
                $query->where('tnx', $tnx);
            })
            ->when($request->filled('status') && $request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('type') && $request->input('type'), function ($query) use ($request) {
                match ($request->input('type')) {
                    'agent-profit' => $query->agentProfit(),
                    'all-deposit' => $query->deposit(),
                    default => $query->where('type', $request->input('type')),
                };
            })
            ->when($request->filled(['from_date', 'to_date']), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('from_date'))
                    ->whereDate('created_at', '<=', $request->input('to_date'));
            })
            ->when($request->filled('wallet_id'), function ($query) use ($request) {
                $query->where('wallet_type', $request->input('wallet_id'));
            })
            ->latest();

        if ($request->has('export')) {
            return $this->exportTransactions($transactions->get(), $user->role->value);
        }

        $transactions = $transactions->paginate($request->integer('per_page', 15));

        return $this->success([
            'transactions' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ], __('Transactions'));
    }

    public function exportTransactions($transactions, $userRole = 'User')
    {
        return (new TransactionsExport($transactions, userRole: $userRole))->download('transactions.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
