<?php

namespace App\Http\Controllers\Test;

use App\Enums\MerchantStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\UserWallet;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class PaymentController extends Controller
{
    public function __construct(
        protected ?PersonalAccessToken $token
    ) {
        Debugbar::disable();
    }

    public function getAccessToken(Request $request)
    {
        // Get public key
        $publicKey = $request->get('public_key');

        // Check if public key is provided
        if (is_null($publicKey)) {
            return $this->withError('Please provide a public key', 401);
        }

        // Check if merchant exists with the provided public key
        $merchant = Merchant::where('public_key', $publicKey)
            ->where('status', MerchantStatus::Approved)
            ->first();

        // If merchant does not exist, return error
        if (! $merchant) {
            return $this->withError('Invalid public key', 401);
        }

        // Create access token
        $accessToken = cache()->remember('access-token-'.$publicKey, now()->addMinutes(30), function () use ($merchant) {
            return $merchant->createToken('access_token', ['*'], now()->addMinutes(30));
        });

        // Return access token
        return $this->withSuccess([
            'token' => $accessToken->plainTextToken,
            'expires_in' => $accessToken->accessToken->expires_at->toDateTimeString(),
        ]);
    }

    public function makePayment(Request $request)
    {
        // Check if token is provided and valid
        if (! $this->checkingToken($request)) {
            return $this->withError('Token is invalid or expired', 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'currency' => 'required|string',
            'transaction_id' => 'required|unique:transactions,tnx',
            'description' => 'required|string|max:20',
            'ipn_url' => 'nullable|string|max:255',
            'callback_url' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:50',
            'customer_email' => 'nullable|string|max:50',
        ]);

        // If validation fails, return validation error
        if ($validator->fails()) {
            return $this->withError($validator->errors()->all(), 400);
        }

        // Get the merchant
        $merchant = $this->token->tokenable;

        // Get wallet via currency code
        $wallet = UserWallet::where('user_id', $merchant->user_id)->whereRelation('currency', 'code', $request->currency)->first();

        // Get site currency
        $siteCurrency = setting('site_currency');

        // Create transaction
        $transaction = Transaction::withoutEvents(function () use ($request, $siteCurrency, $wallet) {
            return Transaction::create([
                'amount' => $request->amount,
                'tnx' => $request->transaction_id,
                'wallet_type' => $siteCurrency == $request->currency ? 'default' : $wallet->id,
                'pay_currency' => $request->currency,
                'type' => TxnType::Payment,
                'description' => $request->description,
                'callback_url' => $request->callback_url,
                'status' => TxnStatus::Pending,
                'method' => 'API',
                'manual_field_data' => [
                    'customer_name' => $request->customer_name,
                    'ipn_url' => $request->get('ipn_url'),
                    'customer_email' => $request->customer_email,
                ],
            ]);
        });

        return $this->withSuccess([
            'payment_url' => route('pay', $transaction->tnx),
        ]);
    }

    private function checkingToken($request): bool
    {
        // Find token by bearer token
        $token = PersonalAccessToken::findToken($request->bearerToken());

        // Assign token to token instance
        $this->token = $token;

        // Check if token is valid and not expired
        return $token && ! ($token->expires_at && $token->expires_at->isPast());
    }

    private function withError(string|array $message, int $statusCode = 402)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }

    private function withSuccess(array $data)
    {
        return response()->json(array_merge(['status' => 'success'], $data));
    }
}
