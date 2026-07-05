<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TransactionController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:transaction-list', ['only' => ['transactions']]),
            new Middleware('permission:admin-profits', ['only' => ['adminProfits']]),
            new Middleware('permission:virtual-card-list', ['only' => ['virtualCardsList']]),
        ];
    }

    public function transactions(Request $request, $id = null)
    {
        $transactions = $this->getTransactions($request);
        $queries = $request->query();

        return view('backend.transaction.index', ['transactions' => $transactions, 'queries' => $queries]);
    }

    private function getTransactions(Request $request, $export = false)
    {
        $perPage = $request->perPage ?? 15;

        $status = $request->status ?? 'all';
        $search = $request->search ?? null;
        $type = $request->type ?? 'all';

        $transactions = Transaction::with('user')
            ->search($search)
            ->status($status)
            ->type($type)
            ->when(in_array(request('sort_field'), ['created_at', 'final_amount', 'type', 'description']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            });

        if ($export) {
            return $transactions->take($perPage)->get();
        }

        $transactions = $transactions->paginate($perPage)->withQueryString();

        return $transactions;
    }

    public function exportCsv(Request $request)
    {
        $transactions = $this->getTransactions($request, true);

        return (new TransactionsExport($transactions, 'Admin'))->download('transactions.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function adminProfits(Request $request)
    {
        $perPage = $request->integer('perPage') ?: 15;

        $search = $request->search ?? null;
        $type = $request->type ?? 'all';
        $profits = Transaction::with('user')
            ->where('charge', '>', 0)
            ->search($search)
            ->status(TxnStatus::Success)
            ->type($type)
            ->when(in_array(request('sort_field'), ['created_at', 'final_amount', 'type', 'description', 'username']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->paginate($perPage)
            ->withQueryString();

        $totalProfits = Transaction::with('user')
            ->where('charge', '>', 0)
            ->status(TxnStatus::Success)
            ->sum('charge');

        return view('backend.transaction.admin-profits', ['profits' => $profits, 'totalProfits' => $totalProfits]);
    }

    public function virtualCardsList()
    {
        $cards = Card::with('user')
            ->when(in_array(request('sort_field'), ['created_at', 'balance', 'card_number', 'expiration_year']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->latest()
            ->paginate();

        return view('backend.virtual_cards.index', compact('cards'));
    }
}
