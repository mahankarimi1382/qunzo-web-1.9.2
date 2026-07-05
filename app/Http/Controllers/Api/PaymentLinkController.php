<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceType;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentLinkResource;
use App\Models\Invoice;
use App\Services\PaymentLinkService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PaymentLinkController extends Controller
{
    use ApiResponseTrait;

    public function history(Request $request)
    {
        $paymentLinks = Invoice::with('transaction')
            ->where('user_id', $request->user()->id)
            ->where('type', InvoiceType::PaymentLink)
            ->when($request->search, fn ($query, $search) => $query->whereLike('number', '%'.$search.'%'))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success([
            'payment_links' => PaymentLinkResource::collection($paymentLinks),
            'pagination' => [
                'current_page' => $paymentLinks->currentPage(),
                'last_page' => $paymentLinks->lastPage(),
                'per_page' => $paymentLinks->perPage(),
                'total' => $paymentLinks->total(),
            ],
        ], __('Payment link history'));
    }

    public function create(Request $request, PaymentLinkService $paymentLinkService)
    {
        $validation = $paymentLinkService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $paymentLink = $paymentLinkService->createPaymentLink($request);

        return $this->success([
            'payment_link' => $paymentLink,
        ], __('Payment link created successfully'));
    }
}
