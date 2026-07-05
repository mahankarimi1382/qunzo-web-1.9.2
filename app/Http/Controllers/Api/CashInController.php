<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashInResource;
use App\Services\CashInService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashInController extends Controller
{
    use ApiResponseTrait;

    protected $cashInService;

    public function __construct(CashInService $cashInService)
    {
        $this->cashInService = $cashInService;
    }

    public function config()
    {

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('cashin_minimum', 'cashin'),
                'maximum_amount' => setting('cashin_maximum', 'cashin'),
                'daily_limit' => setting('cashin_daily_limit', 'cashin'),
                'monthly_limit' => setting('cashin_monthly_limit', 'cashin'),
                'charge' => setting('cashin_charge', 'cashin'),
                'charge_type' => setting('cashin_charge_type', 'cashin'),
            ],
        ], __('Cash-in configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->cashInService->validateCashIn($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->cashInService->cashIn($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(new CashInResource($result['agent_transaction']), __('Cash-in completed successfully!'));
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
            'type' => 'nullable|in:cash_out',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $query = $this->cashInService->getCashInHistory($request);
        $cashIns = $query->paginate($request->per_page ?? 15);

        return $this->success([
            $request->get('type', 'cash_in') => CashInResource::collection($cashIns),
            'pagination' => [
                'current_page' => $cashIns->currentPage(),
                'last_page' => $cashIns->lastPage(),
                'per_page' => $cashIns->perPage(),
                'total' => $cashIns->total(),
            ],
        ], __('Cash-in history'));
    }
}
