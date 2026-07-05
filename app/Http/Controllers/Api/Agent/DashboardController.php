<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $wallets = UserWallet::query()
            ->with('currency')
            ->whereBelongsTo($user)
            ->oldest()
            ->get();

        $transactions = Transaction::with('userWallet.currency')->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return $this->success([
            'transactions' => TransactionResource::collection($transactions),
            'wallets' => (new WalletResource($wallets))->withDefaultWallet($request),
        ], __('Dashboard data'));
    }

    public function markNotification()
    {
        request()->user()->notifications()->update(['read' => true]);

        return $this->success(null, __('All Notifications marked as read'));
    }
}
