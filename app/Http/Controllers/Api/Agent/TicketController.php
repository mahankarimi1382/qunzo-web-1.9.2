<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Services\TicketService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    use ApiResponseTrait;

    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function config()
    {
        $user = auth()->user();

        if (! setting('user_ticket', 'permission')) {
            return $this->error(__('Support feature is not enabled!'));
        }

        return $this->success([
            'settings' => [
                'max_attachments' => 5,
                'max_file_size' => '2MB',
                'allowed_file_types' => ['jpeg', 'png', 'jpg', 'gif', 'svg', 'pdf', 'doc', 'docx'],
                'max_title_length' => 255,
                'max_message_length' => 5000,
            ],
        ], __('Support ticket configuration'));
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'subject' => 'nullable|string',
            'status' => 'nullable|in:open,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $query = $this->ticketService->getTickets($request);
        $tickets = $query->paginate($request->per_page ?? 15);

        return $this->success([
            'tickets' => TicketResource::collection($tickets),
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ], __('Support tickets'));
    }

    public function store(Request $request)
    {
        $validation = $this->ticketService->validateCreate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->ticketService->createTicket($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(
            new TicketResource($result),
            __('Support ticket created successfully!')
        );
    }

    public function show($uuid)
    {
        $user = auth()->user();

        $ticket = $this->ticketService->findTicket($uuid, $user->id);

        if (isValidationException($ticket)) {
            return $this->error($ticket->getMessage(), 404, $ticket->errors());
        }

        $messages = $this->ticketService->getTicketMessages($ticket);

        return $this->success([
            'ticket' => new TicketResource($ticket),
            'messages' => TicketMessageResource::collection($messages),
        ], __('Ticket details'));
    }

    public function reply(Request $request, $uuid)
    {
        $result = $this->ticketService->replyToTicket($request, $uuid);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'ticket' => new TicketResource($result['ticket']),
            'message' => new TicketMessageResource($result['message']),
        ], __('Ticket reply sent successfully!'));
    }

    public function close($uuid)
    {
        $result = $this->ticketService->closeTicket($uuid);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(
            new TicketResource($result),
            __('Ticket closed successfully!')
        );
    }

    public function reopen($uuid)
    {
        $result = $this->ticketService->reopenTicket($uuid);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(
            new TicketResource($result),
            __('Ticket reopened successfully!')
        );
    }

    public function action($uuid)
    {
        $user = auth()->user();

        $ticket = $this->ticketService->findTicket($uuid, $user->id);

        if (isValidationException($ticket)) {
            return $this->error($ticket->getMessage(), 404, $ticket->errors());
        }

        try {
            if ($ticket->isClosed()) {
                $result = $this->ticketService->reopenTicket($uuid);
                $message = __('Ticket reopened successfully!');
            } else {
                $result = $this->ticketService->closeTicket($uuid);
                $message = __('Ticket closed successfully!');
            }

            if (isValidationException($result)) {
                return $this->error($result->getMessage(), 422, $result->errors());
            }

            return $this->success(
                new TicketResource($result),
                $message
            );

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
