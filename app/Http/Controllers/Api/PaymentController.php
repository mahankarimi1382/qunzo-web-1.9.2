<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentHistoryResource;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $validation = $this->paymentService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->paymentService->processPayment($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        if (! $result) {
            return $this->error(__('Sorry! Something went wrong.'), 400);
        }

        return $this->success([
            'transaction' => $result,
            'message' => __('Payment successful!'),
        ]);
    }

    public function index(Request $request)
    {
        return $this->success([
            'minimum_amount' => setting('payment_minimum', 'make_payment'),
            'maximum_amount' => setting('payment_maximum', 'make_payment'),
            'user_charge' => setting('user_make_payment_charge', 'make_payment'),
            'user_charge_type' => setting('user_make_payment_charge_type', 'make_payment'),
            'merchant_charge' => setting('merchant_make_payment_charge', 'make_payment'),
            'merchant_charge_type' => setting('merchant_make_payment_charge_type', 'make_payment'),
        ], __('Payment configuration'));
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'tnx_id' => 'nullable|string',
            'status' => 'nullable|in:Success,Pending,Failed,Cancelled',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator);
        }

        $transactions = Transaction::query()
            ->with('userWallet.currency', 'fromUser')
            ->where('user_id', $user->id)
            ->where('type', 'Payment')
            ->when($request->tnx_id, fn ($q) => $q->where('tnx', 'like', '%'.$request->tnx_id.'%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return $this->success([
            'transactions' => PaymentHistoryResource::collection($transactions),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ], __('Payment history'));
    }
}
