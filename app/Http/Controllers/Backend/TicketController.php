<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller implements HasMiddleware
{
    use ImageUpload;
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:support-ticket-list|support-ticket-action', ['only' => ['index']]),
            new Middleware('permission:support-ticket-action', ['only' => ['closeNow', 'reply', 'show']]),
        ];
    }

    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $status = $request->status ?? 'all';
        $tickets = Ticket::with(['messages.user', 'user'])
            ->has('user')
            ->search($search)
            ->status($status)
            ->when(request('query') != null, function ($query) {
                $query->where('title', 'LIKE', '%'.request('query').'%');
            })
            ->when(in_array(request('sort_field'), ['created_at', 'title', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->paginate($perPage);

        return view('backend.ticket.index', ['tickets' => $tickets]);
    }

    public function show($uuid)
    {
        $ticket = Ticket::with(['messages.user', 'messages.admin'])->uuid($uuid);

        return view('backend.ticket.show', ['ticket' => $ticket]);
    }

    public function closeNow($uuid)
    {
        try {
            Ticket::uuid($uuid)->close();

            $status = 'success';
            $message = __('Ticket Closed successfully');

            notify()->$status($message, $status);

            return Redirect::route('admin.ticket.index');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();

        try {
            $adminId = Auth::id();

            $attachments = [];

            foreach ($request->file('attachments', []) as $attach) {
                $attachments[] = self::imageUploadTrait($attach);
            }

            $data = [
                'model' => 'admin',
                'user_id' => $adminId,
                'message' => nl2br($request->message),
                'attachments' => $attachments,
            ];

            $ticket = Ticket::uuid($request->uuid);
            $user = $ticket->user;

            if ($ticket->isClosed()) {
                $ticket->reopen();
            }

            $ticket->messages()->create(
                [
                    'model' => 'admin',
                    'user_id' => $adminId,
                    'message' => nl2br($request->message),
                    'attach' => $attachments,
                ]
            );

            $code = match (strtolower($user->role->value)) {
                'merchant' => ['merchant_ticket_reply', 'Merchant', ''],
                'user' => ['user_ticket_reply', 'User', ''],
                'agent' => ['agent_ticket_reply', 'Agent', ''],
            };

            $shortcodes = [
                '[[title]]' => $ticket->title,
                '[[message]]' => $data['message'],
                '[[reply_link]]' => $code[2],
                '[[site_title]]' => setting('site_title', 'global'),
            ];

            $this->sendNotify($user->email, $code[0], $code[1], $shortcodes, $user->phone, $user->id, $code[2]);

            DB::commit();

            $status = 'success';
            $message = __('Ticket Reply successfully');

            notify()->$status($message, $status);

            return Redirect::route('admin.ticket.show', $ticket->uuid);
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
            notify()->error(__('Sorry! Something went wrong.'));

            return back();
        }
    }
}
