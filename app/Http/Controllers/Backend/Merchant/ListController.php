<?php

namespace App\Http\Controllers\Backend\Merchant;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ListController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:merchant-list|merchant-mail-send|merchant-basic-manage|merchant-change-password|all-type-status|merchant-balance-add-or-subtract', ['only' => ['index', 'activeUser', 'disabled', 'mailSendAll', 'mailSend']]),
            new Middleware('permission:merchant-mail-send', ['only' => ['mailSendAll', 'mailSend']]),
            new Middleware('permission:merchant-basic-manage', ['only' => ['update']]),
            new Middleware('permission:merchant-change-password', ['only' => ['passwordUpdate']]),
            new Middleware('permission:all-type-status', ['only' => ['statusUpdate']]),
            new Middleware('permission:merchant-delete', ['only' => ['destroy']]),
        ];
    }

    public function index($type = 'all')
    {
        $sortField = request('sort_field');
        $sortDir = request('sort_dir', 'asc');
        $search = request('query') ?? null;
        $status = request('status') ?? 'all';

        $query = Merchant::with('user')->has('user')->when(! blank($sortField), function ($query) use ($sortField, $sortDir) {
            if (in_array($sortField, ['status'])) {
                $query->orderBy($sortField, $sortDir);
            } elseif (in_array($sortField, ['email', 'balance', 'username'])) {
                $query->join('users', 'users.id', '=', 'merchants.user_id')
                    ->orderBy("users.{$sortField}", $sortDir)
                    ->select('merchants.*');
            }
        })->when(blank($sortField), fn ($query) => $query->latest())->search($search)->status($status);

        $merchants = match ($type) {
            'pending' => $query->where('status', MerchantStatus::Pending)->paginate(),
            'rejected' => $query->where('status', MerchantStatus::Rejected)->paginate(),
            'approved' => $query->where('status', MerchantStatus::Approved)->paginate(),
            'all' => $query->paginate(),
            default => abort(404),
        };

        return view('backend.merchant.index', ['merchants' => $merchants]);
    }

    public function edit($id)
    {
        $merchant = Merchant::findOrFail($id);

        $user = $merchant->user;

        $transactions = null;
        $tickets = null;

        if (request('tab') == 'transactions') {
            $transactions = Transaction::with('userWallet.currency')->where('user_id', $user->id)
                ->search(request('query'))
                ->when(request('type') != null, function ($query) {
                    $query->where('type', request('type'));
                })
                ->when(request('sort_field') != null, function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'ticket') {
            $tickets = Ticket::where('user_id', $user->id)
                ->when(request('query') != null, function ($query) {
                    $query->where('title', 'LIKE', '%'.request('query').'%');
                })
                ->when(in_array(request('sort_field'), ['created_at', 'title', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        }

        $statistics = [
            'total_payments' => $user->totalPaymentsCount(),
            'total_withdraw' => $user->totalWithdrawCount(),
            'total_tickets' => $user->tickets()->count(),
        ];

        return view('backend.merchant.edit', ['merchant' => $merchant, 'user' => $user, 'statistics' => $statistics, 'transactions' => $transactions, 'tickets' => $tickets]);
    }
}
