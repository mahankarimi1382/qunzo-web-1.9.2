<?php

namespace App\Http\Controllers\Backend\Agent;

use App\Enums\AgentStatus;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AgentListController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:agent-list|agent-mail-send|agent-basic-manage|agent-change-password|all-type-status|agent-balance-add-or-subtract', ['only' => ['index', 'activeUser', 'disabled', 'mailSendAll', 'mailSend']]),
            new Middleware('permission:agent-mail-send', ['only' => ['mailSendAll', 'mailSend']]),
            new Middleware('permission:agent-basic-manage', ['only' => ['update']]),
            new Middleware('permission:agent-change-password', ['only' => ['passwordUpdate']]),
            new Middleware('permission:all-type-status', ['only' => ['statusUpdate']]),
            new Middleware('permission:agent-delete', ['only' => ['destroy']]),
        ];
    }

    public function index($type = 'all')
    {
        $sortField = request('sort_field');
        $search = request('query') ?? null;
        $status = request('status') ?? 'all';

        $query = Agent::query()
            ->has('user')
            ->with('user')
            ->when(blank($sortField), fn ($query) => $query->latest())->search($search)
            ->status($status);

        $agents = match ($type) {
            'pending' => $query->where('status', AgentStatus::Pending)->paginate(),
            'rejected' => $query->where('status', AgentStatus::Rejected)->paginate(),
            'approved' => $query->where('status', AgentStatus::Approved)->paginate(),
            'all' => $query->paginate(),
            default => abort(404),
        };

        return view('backend.agent.index', ['agents' => $agents]);
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $user = $agent->user;

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
                ->paginate(10)
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
            'total_cashin' => $user->totalCashinCount(),
            'total_cash_received' => $user->totalCashReceivedCount(),
            'total_tickets' => $user->tickets()->count(),
        ];

        return view('backend.agent.edit', ['agent' => $agent, 'user' => $user, 'statistics' => $statistics, 'transactions' => $transactions, 'tickets' => $tickets]);
    }
}
