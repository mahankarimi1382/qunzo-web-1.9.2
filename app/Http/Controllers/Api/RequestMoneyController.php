<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MoneyRequestResource;
use App\Models\MoneyRequest;
use App\Services\RequestMoneyService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestMoneyController extends Controller
{
    use ApiResponseTrait;

    protected $requestMoneyService;

    public function __construct(RequestMoneyService $requestMoneyService)
    {
        $this->requestMoneyService = $requestMoneyService;
    }

    public function config()
    {
        $user = auth()->user();

        // Check if request money is enabled
        if (! setting('user_request_money', 'permission')) {
            return $this->error(__('Request money feature is not enabled!'));
        }

        $wallets = collect();

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('request_money_minimum', 'request_money'),
                'maximum_amount' => setting('request_money_maximum', 'request_money'),
                'daily_limit' => setting('request_money_daily_limit', 'request_money'),
                'charge' => setting('request_money_charge', 'request_money'),
                'charge_type' => setting('request_money_charge_type', 'request_money'),
            ],
        ], __('Request money configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->requestMoneyService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->requestMoneyService->createRequest($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }
        if (! $result) {
            return $this->error(__('Sorry! Something went wrong.'), 422);
        }

        return $this->success([
            'request' => new MoneyRequestResource($result),
        ], __('Request money sent successfully!'));
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'status' => 'nullable|in:pending,success,rejected',
            'type' => 'nullable|in:sent,received',
            'date_from' => 'nullable|date|date_format:Y-m-d|before_or_equal:date_to',
            'date_to' => 'nullable|date|date_format:Y-m-d|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $query = MoneyRequest::with(['requester', 'recipient']);

        // Filter by type (sent or received)
        if ($request->type === 'sent') {
            $query->where('requester_user_id', $user->id);
        } elseif ($request->type === 'received') {
            $query->where('recipient_user_id', $user->id);
        } else {
            // Show both sent and received
            $query->where(function ($q) use ($user) {
                $q->where('requester_user_id', $user->id)
                    ->orWhere('recipient_user_id', $user->id);
            });
        }

        // Apply filters
        $query->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $requests = $query->latest()->paginate($request->integer('per_page', 15));

        return $this->success([
            'requests' => MoneyRequestResource::collection($requests),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ], __('Request money :type history', ['type' => $request->type ?? 'all']));
    }

    public function action(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:accept,reject',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $result = $this->requestMoneyService->processRequestAction($id, $request->action);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'request' => new MoneyRequestResource($result),
        ], __('Request money action processed successfully!'));
    }
}
