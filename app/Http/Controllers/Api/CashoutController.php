<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashoutResource;
use App\Models\UserWallet;
use App\Services\CashoutService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashoutController extends Controller
{
    use ApiResponseTrait;

    protected $cashoutService;

    public function __construct(CashoutService $cashoutService)
    {
        $this->cashoutService = $cashoutService;
    }

    public function config()
    {
        $user = auth()->user();

        // Check if cashout feature is enabled
        if (! setting('user_cashout', 'permission')) {
            return $this->error(__('Cashout feature is not enabled!'));
        }

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('cashout_minimum', 'cashout'),
                'maximum_amount' => setting('cashout_maximum', 'cashout'),
                'daily_limit' => setting('cashout_daily_limit', 'cashout'),
                'monthly_limit' => setting('cashout_monthly_limit', 'cashout'),
                'charge' => setting('cashout_charge', 'cashout'),
                'charge_type' => setting('cashout_charge_type', 'cashout'),
                'agent_commission' => setting('cashout_agent_commission', 'cashout'),
                'agent_commission_type' => setting('cashout_agent_commission_type', 'cashout'),
            ],
        ], __('Cashout configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->cashoutService->validateCashout($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->cashoutService->cashout($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(new CashoutResource($result['user_transaction']), __('Cashout completed successfully!'));
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

        $query = $this->cashoutService->getCashoutHistory($request);
        $cashouts = $query->paginate($request->per_page ?? 15);

        return $this->success([
            'cashouts' => CashoutResource::collection($cashouts),
            'pagination' => [
                'current_page' => $cashouts->currentPage(),
                'last_page' => $cashouts->lastPage(),
                'per_page' => $cashouts->perPage(),
                'total' => $cashouts->total(),
            ],
        ], __('Cashout history'));
    }

    public function show($id)
    {
        $user = auth()->user();

        $cashout = $this->cashoutService->getCashoutHistory(request())
            ->where('id', $id)
            ->first();

        if (! $cashout) {
            return $this->error(__('Cashout not found'), 404);
        }

        return $this->success(
            new CashoutResource($cashout),
            __('Cashout details')
        );
    }

    public function calculateCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'wallet_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $user = auth()->user();
        $amount = $request->amount;
        $wallet = null;
        $currency = setting('site_currency', 'global');

        if ($request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', $user->id)
                ->where('id', $request->wallet_id)
                ->with('currency')
                ->first();

            if (! $wallet) {
                return $this->error(__('Wallet not found'));
            }

            $currency = $wallet->currency->code;
        }

        // Calculate charge
        $cashoutCharge = setting('cashout_charge', 'cashout');
        $cashoutChargeType = setting('cashout_charge_type', 'cashout');

        if ($cashoutChargeType === 'percentage') {
            $charge = ($cashoutCharge * $amount) / 100;
        } else {
            $charge = $wallet
                ? $cashoutCharge * $wallet->currency->conversion_rate
                : $cashoutCharge;
        }

        $totalAmount = $amount + $charge;

        return $this->success([
            'amount' => $amount,
            'charge' => round($charge, 2),
            'total_amount' => round($totalAmount, 2),
            'currency' => $currency,
            'currency_symbol' => $wallet ? $wallet->currency->symbol : setting('currency_symbol', 'global'),
        ], __('Charge calculated'));
    }

    public function verifyAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $user = auth()->user();
        $agent = \App\Models\User::has('agent')
            ->where('account_number', $request->agent_number)
            ->whereNot('id', $user->id)
            ->first();

        if (! $agent) {
            return $this->error(__('Agent not found'));
        }

        if (! $agent->status) {
            return $this->error(__('Agent account is inactive'));
        }

        return $this->success([
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->full_name,
                'email' => $agent->email,
                'account_number' => $agent->account_number,
                'avatar' => $agent->avatar,
            ],
        ], __('Agent verified'));
    }
}
