<?php

namespace App\Http\Controllers\Api\Payment;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Merchant;
use App\Models\SandboxTransaction;
use App\Models\Transaction;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SandboxPaymentController extends BasePaymentController
{
    public function makePayment(Request $request)
    {
        // Check if token is provided and valid
        if (! $this->checkingToken($request)) {
            return $this->withError('Token is invalid or expired', 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'currency' => 'required|string|max:4',
            'transaction_id' => 'required|max:12',
            'description' => 'required|string|max:20',
            'ipn_url' => 'nullable|string|url|max:255',
            'callback_url' => 'nullable|string|url|max:255',
            'customer_name' => 'nullable|string|max:50',
            'customer_email' => 'nullable|string|max:50',
        ]);

        // If validation fails, return validation error
        if ($validator->fails()) {
            return $this->withError($validator->errors()->all(), 400);
        }

        // Get the merchant
        $merchant = $this->token->tokenable;

        // Get site currency
        $siteCurrency = setting('site_currency');

        // Get wallet via currency code
        $wallet = UserWallet::where('user_id', $merchant->user_id)->whereRelation('currency', 'code', $request->currency)->first();

        if ($siteCurrency != $request->currency && ! $wallet) {
            return $this->withError('Wallet not found', 400);
        }

        // Create transaction
        $transaction = SandboxTransaction::create([
            'amount' => $request->amount,
            'final_amount' => $request->amount,
            'user_id' => $merchant->user_id,
            'wallet_type' => $siteCurrency == $request->currency ? 'default' : $wallet->id,
            'pay_currency' => $request->currency,
            'type' => TxnType::Payment,
            'description' => $request->description,
            'callback_url' => $request->callback_url,
            'status' => TxnStatus::Pending,
            'method' => 'API',
            'manual_field_data' => [
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'ipn_url' => $request->get('ipn_url'),
                'transaction_id' => $request->transaction_id,
            ],
        ]);

        return $this->withSuccess([
            'payment_url' => route('sandbox.pay', $transaction->tnx),
        ]);
    }
}
