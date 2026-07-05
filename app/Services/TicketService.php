<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Ticket;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function rand;

class TicketService
{
    use ImageUpload, NotifyTrait;

    public function createTicket($request)
    {
        $user = $request->user();

        try {

            $availabilityCheck = $this->checkAvailability($user);
            if (isValidationException($availabilityCheck)) {
                return $availabilityCheck;
            }

            $validation = $this->validateCreate($request);
            if (isValidationException($validation)) {
                return $validation;
            }

            DB::beginTransaction();

            $attachments = $this->processAttachments($request);

            $ticketData = [
                'uuid' => 'SUPT' . rand(100000, 999999),
                'title' => $request->title,
                'message' => nl2br($request->message),
                'attachments' => $attachments,
                'status' => 'open',
            ];

            $ticket = $user->tickets()->create($ticketData);

            $this->sendTicketCreatedNotifications($user, $ticket);

            DB::commit();

            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();

            return makeValidationException([
                'message' => [$e->getMessage()],
            ]);
        }
    }

    public function replyToTicket($request, $uuid)
    {
        $user = $request->user();

        try {

            $validation = $this->validateReply($request);
            if (isValidationException($validation)) {
                return $validation;
            }

            $ticket = $this->findTicket($uuid, $user->id);
            if (isValidationException($ticket)) {
                return $ticket;
            }

            DB::beginTransaction();

            if ($ticket->isClosed()) {
                $ticket->reopen();
            }

            $attachments = $this->processAttachments($request);

            $messageData = [
                'user_id' => $user->id,
                'message' => nl2br($request->message),
                'attach' => ($attachments),
            ];

            $message = $ticket->messages()->create($messageData);

            $ticket->touch();

            $this->sendTicketReplyNotifications($user, $ticket, $message);

            DB::commit();

            return [
                'ticket' => $ticket,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return makeValidationException([
                'message' => [$e->getMessage()],
            ]);
        }
    }

    public function closeTicket($uuid)
    {
        $user = request()->user();

        try {
            $ticket = $this->findTicket($uuid, $user->id);
            if (isValidationException($ticket)) {
                return $ticket;
            }

            if ($ticket->isClosed()) {
                return makeValidationException([
                    'ticket' => [__('Ticket is already closed')],
                ]);
            }

            DB::beginTransaction();

            $ticket->close();

            $this->sendTicketClosedNotifications($user, $ticket);

            DB::commit();

            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reopenTicket($uuid)
    {
        $user = request()->user();

        try {
            $ticket = $this->findTicket($uuid, $user->id);
            if (isValidationException($ticket)) {
                return $ticket;
            }

            if (! $ticket->isClosed()) {
                return makeValidationException([
                    'ticket' => [__('Ticket is already open')],
                ]);
            }

            DB::beginTransaction();

            $ticket->reopen();

            $this->sendTicketReopenedNotifications($user, $ticket);

            DB::commit();

            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function checkAvailability($user)
    {
        if (! setting('user_ticket', 'permission')) {
            return makeValidationException([
                'support' => [__('Support ticket feature is disabled')],
            ]);
        }

        return true;
    }

    public function validateCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:5000',
        ], [
            'title.required' => __('Please enter a ticket title'),
            'message.required' => __('Please enter your message'),
            'attachments.max' => __('You can upload maximum 5 attachments'),
            'attachments.*.max' => __('Each attachment must be less than 5 MB'),
            'attachments.*.mimes' => __('Only jpeg, png, jpg, gif, svg, pdf, doc, docx files are allowed'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        return true;
    }

    public function validateReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:5000',
        ], [
            'message.required' => __('Please enter your reply message'),
            'attachments.max' => __('You can upload maximum 5 attachments'),
            'attachments.*.max' => __('Each attachment must be less than 5MB'),
            'attachments.*.mimes' => __('Only jpeg, png, jpg, gif, svg, pdf, doc, docx files are allowed'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        return true;
    }

    public function findTicket($uuid, $userId)
    {
        $ticket = Ticket::where('uuid', $uuid)
            ->where('user_id', $userId)
            ->with(['user'])
            ->first();

        if (! $ticket) {
            return makeValidationException([
                'ticket' => [__('Ticket not found or access denied')],
            ]);
        }

        return $ticket;
    }

    public function processAttachments($request)
    {
        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $this->imageUploadTrait($file);
            }
        }

        return $attachments;
    }

    public function getTickets($request)
    {
        $user = $request->user();

        $query = Ticket::where('user_id', $user->id)
            ->with(['messages.user', 'user'])
            ->withCount('messages');

        $query->when($request->subject, function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->subject . '%')
                ->orWhere('uuid', 'like', '%' . $request->subject . '%');
        })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        return $query->latest();
    }

    public function getTicketMessages($ticket)
    {
        return Message::where('ticket_id', $ticket->id)
            ->where('user_id', $ticket->user_id)
            ->with(['admin', 'user'])
            ->get();
    }

    private function sendTicketCreatedNotifications($user, $ticket)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $ticket->uuid,
            '[[title]]' => $ticket->title,
            '[[message]]' => strip_tags($ticket->message),
            '[[priority]]' => ucfirst($ticket->priority),
            '[[status]]' => strtoupper($ticket->status?->value ?? $ticket->status),
            '[[created_at]]' => $ticket->created_at->format('d M, Y h:i A'),
            '[[ticket_link]]' => route('admin.ticket.show', $ticket->uuid),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
        ];

        $this->sendNotify(
            setting('support_email', 'global'),
            'admin_new_ticket',
            'Admin',
            $shortcodes,
            null,
            null,
            route('admin.ticket.show', $ticket->uuid)
        );

        $this->pushNotify(
            'admin_new_ticket',
            $shortcodes,
            route('admin.ticket.show', $ticket->uuid),
            $user->id,
            'Admin'
        );
    }

    private function sendTicketReplyNotifications($user, $ticket, $message)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $ticket->uuid,
            '[[title]]' => $ticket->title,
            '[[message]]' => strip_tags($message->message),
            '[[status]]' => strtoupper($ticket->status?->value ?? $ticket->status),
            '[[replied_at]]' => $message->created_at->format('d M, Y h:i A'),
            '[[ticket_link]]' => route('admin.ticket.show', $ticket->uuid),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
        ];

        $this->sendNotify(
            setting('support_email', 'global'),
            'admin_ticket_reply',
            'Admin',
            $shortcodes,
            null,
            null,
            route('admin.ticket.show', $ticket->uuid)
        );

        $this->pushNotify(
            'support_ticket_reply',
            $shortcodes,
            route('admin.ticket.show', $ticket->uuid),
            $user->id,
            'Admin'
        );
    }

    private function sendTicketClosedNotifications($user, $ticket)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $ticket->uuid,
            '[[title]]' => $ticket->title,
            '[[status]]' => strtoupper($ticket->status?->value ?? $ticket->status),
            '[[closed_at]]' => $ticket->updated_at->format('d M, Y h:i A'),
            '[[ticket_link]]' => '',
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $user->email,
            'user_ticket_closed',
            'User',
            $shortcodes,
            $user->phone,
            $user->id,
            ''
        );
    }

    private function sendTicketReopenedNotifications($user, $ticket)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $ticket->uuid,
            '[[title]]' => $ticket->title,
            '[[status]]' => strtoupper($ticket->status?->value ?? $ticket->status),
            '[[reopened_at]]' => $ticket->updated_at->format('d M, Y h:i A'),
            '[[ticket_link]]' => route('admin.ticket.show', $ticket->uuid),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            setting('support_email', 'global'),
            'admin_ticket_reopened',
            'Admin',
            $shortcodes,
            null,
            null,
            route('admin.ticket.show', $ticket->uuid)
        );
    }
}
