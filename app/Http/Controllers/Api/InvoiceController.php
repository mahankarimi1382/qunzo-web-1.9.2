<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Currency;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    use ApiResponseTrait;

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function config(Request $request)
    {
        $user = $request->user();

        // Check if invoice is enabled
        if (! setting('user_invoice', 'permission')) {
            return $this->error(__('Invoice feature is not enabled!'));
        }

        $currencies = Currency::where('status', 1)->get();

        return $this->success([
            'currencies' => $currencies,
            'invoice_settings' => [
                'daily_limit' => setting('invoice_daily_limit', 'invoice'),
                'charge' => setting('invoice_charge', 'invoice'),
                'charge_type' => setting('invoice_charge_type', 'invoice'),
            ],
        ], __('Invoice configuration'));
    }

    public function store(Request $request)
    {

        $validation = $this->invoiceService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->invoiceService->createInvoice($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        if (! $result instanceof Invoice && isset($result['success']) && $result['success'] === false) {
            return $this->error($result['message'], 422);
        }

        return $this->success([
            'invoice' => new InvoiceResource($result),
        ], $result['message']);
    }

    public function show($id)
    {
        $user = request()->user();

        $invoice = Invoice::with(['transaction'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_published', true);
            })
            ->find($id);

        if (! $invoice) {
            return $this->error(__('Invoice not found'), 404);
        }

        return $this->success(
            new InvoiceResource($invoice),
            __('Invoice details')
        );
    }

    public function update(Request $request, $id)
    {
        $validation = $this->invoiceService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->invoiceService->updateInvoice($request, $id);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        if (! $result) {
            return $this->error(__('Sorry! Something went wrong.'));
        }

        return $this->success([
            'invoice' => new InvoiceResource($result),
        ], $result['message']);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'status' => 'nullable|in:paid,unpaid,published,draft',
            'search' => 'nullable|string',
            'date_from' => 'nullable|date|date_format:Y-m-d|before_or_equal:date_to',
            'date_to' => 'nullable|date|date_format:Y-m-d|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = $request->user();

        $invoices = Invoice::query()
            ->with(['transaction'])
            ->where('user_id', $user->id)
            ->when($request->status, function ($query) use ($request) {
                switch ($request->status) {
                    case 'paid':
                        $query->where('is_paid', true);
                        break;
                    case 'unpaid':
                        $query->where('is_paid', false);
                        break;
                    case 'published':
                        $query->where('is_published', true);
                        break;
                    case 'draft':
                        $query->where('is_published', false);
                        break;
                }
            })
            ->when($request->search, fn ($q) => $q->where('number', 'like', '%'.$request->search.'%'))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        $invoicesData = InvoiceResource::collection($invoices);

        return $this->success([
            'invoices' => $invoicesData,
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ],
        ], __('Invoice history'));
    }

    public function pay(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $result = $this->invoiceService->payInvoice($request, $id, true);

        if (! $result) {
            return $this->error($result['message']);
        }

        return $this->success([
            'invoice' => new InvoiceResource($result['invoice']),
            'transaction' => $result['transaction'],
        ], $result['message']);
    }

    public function calculateTotal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string|exists:currencies,code',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $subTotal = 0;
        $currency = Currency::where('code', $request->currency)->first();

        foreach ($request->items as $item) {
            $subTotal += $item['quantity'] * $item['unit_price'];
        }

        // Calculate charge
        $invoiceCharge = setting('invoice_charge', 'invoice');
        $invoiceChargeType = setting('invoice_charge_type', 'invoice');
        $chargeCurrency = setting('site_currency', 'global');

        if ($invoiceChargeType === 'percentage') {
            $charge = ($invoiceCharge * $subTotal) / 100;
        } else {
            $charge = $chargeCurrency == $request->currency
                ? $invoiceCharge
                : $invoiceCharge * $currency->conversion_rate;
        }

        $totalAmount = $subTotal + $charge;

        return $this->success([
            'sub_total' => round($subTotal, 2),
            'charge' => round($charge, 2),
            'total_amount' => round($totalAmount, 2),
            'currency' => $currency->code,
            'currency_symbol' => $currency->symbol,
        ], __('Total calculated'));
    }

    public function delete($id)
    {
        $user = request()->user();

        $invoice = Invoice::where('user_id', $user->id)
            ->where('is_paid', false)
            ->findOrFail($id);

        // Delete related transaction
        $invoice->transaction()->delete();

        // Delete invoice
        $invoice->delete();

        return $this->successWithoutData(__('Invoice deleted successfully'));
    }
}
