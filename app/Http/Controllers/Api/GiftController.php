<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GiftResource;
use App\Models\Gift;
use App\Models\UserWallet;
use App\Services\GiftService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiftController extends Controller
{
    use ApiResponseTrait;

    protected $giftService;

    public function __construct(GiftService $giftService)
    {
        $this->giftService = $giftService;
    }

    public function config()
    {
        $user = auth()->user();

        // Check if gift feature is enabled
        if (! setting('user_gift', 'permission')) {
            return $this->error(__('Gift feature is not enabled!'));
        }

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('gift_minimum', 'gift'),
                'maximum_amount' => setting('gift_maximum', 'gift'),
                'daily_limit' => setting('gift_daily_limit', 'gift'),
                'charge' => setting('gift_charge', 'gift'),
                'charge_type' => setting('gift_charge_type', 'gift'),
            ],
        ], __('Gift configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->giftService->validateCreateGift($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->giftService->createGift($request);
        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'gift' => new GiftResource($result),
        ], $result['message']);
    }

    public function redeem(Request $request)
    {
        $validation = $this->giftService->validateRedeemGift($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->giftService->redeemGift($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'gift' => new GiftResource($result['gift']),
        ], $result['message']);
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'code' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $gifts = Gift::query()
            ->with(['currency', 'redeemer'])
            ->where('user_id', $user->id)
            ->when($request->code, fn ($q) => $q->where('code', 'like', '%'.$request->code.'%'))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return $this->success([
            'gifts' => GiftResource::collection($gifts),
            'pagination' => [
                'current_page' => $gifts->currentPage(),
                'last_page' => $gifts->lastPage(),
                'per_page' => $gifts->perPage(),
                'total' => $gifts->total(),
            ],
        ], __('Gift history'));
    }

    public function redeemHistory(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'code' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $gifts = Gift::query()
            ->with(['currency', 'user'])
            ->where('redeemer_id', $user->id)
            ->when($request->code, fn ($q) => $q->where('code', 'like', '%'.$request->code.'%'))
            ->when($request->date_from, fn ($q) => $q->whereDate('claimed_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('claimed_at', '<=', $request->date_to))
            ->latest('claimed_at')
            ->paginate($request->per_page ?? 15);

        return $this->success([
            'gifts' => GiftResource::collection($gifts),
            'pagination' => [
                'current_page' => $gifts->currentPage(),
                'last_page' => $gifts->lastPage(),
                'per_page' => $gifts->perPage(),
                'total' => $gifts->total(),
            ],
        ], __('Redeem history'));
    }

    public function show($id)
    {
        $user = auth()->user();

        $gift = Gift::with(['currency', 'user', 'redeemer'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('redeemer_id', $user->id);
            })
            ->findOrFail($id);

        return $this->success(
            new GiftResource($gift),
            __('Gift details')
        );
    }

    public function calculateCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'wallet_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
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
        $giftCharge = setting('gift_charge', 'gift');
        $giftChargeType = setting('gift_charge_type', 'gift');

        if ($giftChargeType === 'percentage') {
            $charge = ($giftCharge * $amount) / 100;
        } else {
            $charge = $wallet
                ? $giftCharge * $wallet->currency->conversion_rate
                : $giftCharge;
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

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = auth()->user();
        $gift = Gift::where('code', $request->code)
            ->with(['user', 'currency'])
            ->first();

        if (! $gift) {
            return $this->error(__('Gift code not found'));
        }

        if ($gift->redeemer_id) {
            return $this->error(__('Gift code already redeemed'));
        }

        if ($gift->user_id === $user->id) {
            return $this->error(__('You cannot redeem your own gift'));
        }

        return $this->success([
            'gift' => [
                'code' => $gift->code,
                'amount' => $gift->amount,
                'currency' => $gift->currency ? $gift->currency->code : setting('site_currency', 'global'),
                'currency_symbol' => $gift->currency ? $gift->currency->symbol : setting('currency_symbol', 'global'),
                'creator' => [
                    'name' => $gift->user->full_name,
                    'account_number' => $gift->user->account_number,
                ],
                'created_at' => $gift->created_at,
            ],
        ], __('Gift verified successfully'));
    }
}
