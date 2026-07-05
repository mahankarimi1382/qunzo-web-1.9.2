<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddMoneyResource;
use App\Models\DepositMethod;
use App\Services\AddMoneyService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AddMoneyController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $addMoneyService = new AddMoneyService;
        $validation = $addMoneyService->validate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), errors: $validation->errors());
        }

        $txnInfo = $addMoneyService->processAddMoney($request);

        if (isValidationException($txnInfo)) {
            return $this->error($txnInfo->getMessage(), errors: $txnInfo->errors());
        } elseif (! $txnInfo) {
            return $this->error(__('Sorry! Something went wrong. Please try again'));
        }

        if (is_string($txnInfo)) {
            return $this->success(['redirect_url' => $txnInfo]);
        }

        return $this->success(['transaction' => $txnInfo]);
    }

    public function index(Request $request)
    {
        $gateways = DepositMethod::query()
            ->where('status', 1)
            ->when($request->filled('currency'), function ($query) use ($request) {
                $query->where('currency', $request->currency);
            })
            ->get();

        return $this->success(AddMoneyResource::collection($gateways), __('Deposit methods'));
    }
}
