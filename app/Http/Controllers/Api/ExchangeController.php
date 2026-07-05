<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExchangeResource;
use App\Services\CurrencyService;
use App\Services\ExchangeService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{
    use ApiResponseTrait;

    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function config()
    {
        if (! setting('user_exchange', 'permission')) {
            return $this->error(__('Exchange feature is not enabled!'));
        }

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('exchange_minimum', 'exchange'),
                'maximum_amount' => setting('exchange_maximum', 'exchange'),
                'charge' => setting('exchange_charge', 'exchange'),
                'charge_type' => setting('exchange_charge_type', 'exchange'),
                'kyc_required' => setting('kyc_exchange', 'permission'),
            ],
        ], __('Exchange configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->exchangeService->validateExchange($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->exchangeService->exchange($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'transaction' => new ExchangeResource($result['transaction']),
            'exchange_data' => $result['exchange_data'],
        ], __('Currency exchange completed successfully!'));
    }

    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'tnx_id' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:pending,success,failed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $query = $this->exchangeService->getExchangeHistory($request);
        $exchanges = $query->paginate($request->per_page ?? 15);

        return $this->success([
            'exchanges' => ExchangeResource::collection($exchanges),
            'pagination' => [
                'current_page' => $exchanges->currentPage(),
                'last_page' => $exchanges->lastPage(),
                'per_page' => $exchanges->perPage(),
                'total' => $exchanges->total(),
            ],
        ], __('Exchange history'));
    }

    public function show($id)
    {
        $user = request()->user();

        $exchange = $this->exchangeService->getExchangeHistory(request())
            ->where('id', $id)
            ->first();

        if (! $exchange) {
            return $this->error(__('Exchange transaction not found'), 404);
        }

        return $this->success(
            new ExchangeResource($exchange),
            __('Exchange details')
        );
    }

    public function calculateExchange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'from_wallet' => 'required',
            'to_wallet' => 'required|different:from_wallet',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $calculationData = $this->exchangeService->calculateExchangeAmounts($request);

            if (isValidationException($calculationData)) {
                return $this->error($calculationData->getMessage(), 422, $calculationData->errors());
            }

            return $this->success([
                'original_amount' => $calculationData['amount'],
                'charge' => round($calculationData['charge'], 2),
                'total_deduction' => round($calculationData['final_amount'], 2),
                'converted_amount' => round($calculationData['converted_amount'], 2),
                'from_currency' => $calculationData['from_currency'],
                'to_currency' => $calculationData['to_currency'],
                'exchange_rate' => round($calculationData['converted_amount'] / $calculationData['amount'], 4),
            ], __('Exchange calculation'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getExchangeRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_currency' => 'required|string',
            'to_currency' => 'required|string|different:from_currency',
            'amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $amount = $request->amount ?? 1;
            $convertedAmount = CurrencyService::convert(
                $amount,
                $request->from_currency,
                $request->to_currency
            );

            return $this->success([
                'from_currency' => $request->from_currency,
                'to_currency' => $request->to_currency,
                'amount' => $amount,
                'converted_amount' => round($convertedAmount, 4),
                'exchange_rate' => round($convertedAmount / $amount, 4),
            ], __('Exchange rate'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
