<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransferResource;
use App\Models\UserWallet;
use App\Services\TransferMoneyService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransferMoneyController extends Controller
{
    use ApiResponseTrait;

    protected $transferMoneyService;

    public function __construct(TransferMoneyService $transferMoneyService)
    {
        $this->transferMoneyService = $transferMoneyService;
    }

    public function config()
    {
        $user = auth()->user();

        // Check if transfer feature is enabled
        if (! setting('user_transfer', 'permission')) {
            return $this->error(__('Transfer feature is not enabled!'));
        }

        return $this->success([
            'settings' => [
                'minimum_amount' => setting('transfer_minimum', 'transfer'),
                'maximum_amount' => setting('transfer_maximum', 'transfer'),
                'daily_limit' => setting('transfer_daily_limit', 'transfer'),
                'charge' => setting('transfer_charge', 'transfer'),
                'charge_type' => setting('transfer_charge_type', 'transfer'),
                'kyc_required' => setting('kyc_fund_transfer', 'permission'),
            ],
        ], __('Transfer configuration'));
    }

    public function store(Request $request)
    {
        $validation = $this->transferMoneyService->validateTransfer($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->transferMoneyService->transferMoney($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([
            'sender_transaction' => new TransferResource($result['sender_transaction']),
            'receiver_transaction' => new TransferResource($result['receiver_transaction']),
        ], __('Money transferred successfully!'));
    }

    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'type' => 'nullable|in:all,send,receive',
            'tnx_id' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:pending,success,failed',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 401);
        }

        $type = $request->type ?? 'all';
        $query = $this->transferMoneyService->getTransferHistory($request, $type);
        $transfers = $query->paginate($request->per_page ?? 15);

        return $this->success([
            'transfers' => TransferResource::collection($transfers),
            'pagination' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ],
        ], __('Transfer history'));
    }

    public function show($id)
    {
        $user = auth()->user();

        $transfer = $this->transferMoneyService->getTransferHistory(request())
            ->where('id', $id)
            ->first();

        if (! $transfer) {
            return $this->error(__('Transfer not found'), 404);
        }

        return $this->success(
            new TransferResource($transfer),
            __('Transfer details')
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
        $transferCharge = setting('transfer_charge', 'transfer');
        $transferChargeType = setting('transfer_charge_type', 'transfer');

        if ($transferChargeType === 'percentage') {
            $charge = ($transferCharge * $amount) / 100;
        } else {
            $charge = $wallet
                ? $transferCharge * $wallet->currency->conversion_rate
                : $transferCharge;
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

    public function verifyRecipient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $user = auth()->user();
        $recipient = \App\Models\User::user()
            ->where('account_number', $request->account_number)
            ->whereNot('id', $user->id)
            ->first();

        if (! $recipient) {
            return $this->error(__('Recipient not found'));
        }

        if (! $recipient->status) {
            return $this->error(__('Recipient account is inactive'));
        }

        return $this->success([
            'recipient' => [
                'id' => $recipient->id,
                'name' => $recipient->full_name,
                'email' => $recipient->email,
                'account_number' => $recipient->account_number,
                'avatar' => $recipient->avatar,
            ],
        ], __('Recipient verified'));
    }
}
