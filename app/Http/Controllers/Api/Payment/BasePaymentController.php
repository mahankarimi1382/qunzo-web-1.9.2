<?php

namespace App\Http\Controllers\Api\Payment;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class BasePaymentController extends Controller
{
    public function __construct(
        protected ?PersonalAccessToken $token
    ) {
        Debugbar::disable();
    }

    public function getAccessToken(Request $request): JsonResponse
    {
        // Get public key
        $publicKey = $request->get('public_key');

        // Check if public key is provided
        if (is_null($publicKey)) {
            return $this->withError('Please provide a public key', 401);
        }

        // Create access token
        $accessToken = $this->createAccessToken($publicKey);

        if ($accessToken === null) {
            return $this->withError('Invalid public key', 401);
        }

        // Return access token
        return $this->withSuccess([
            'token' => $accessToken->plainTextToken,
            'expires_in' => $accessToken->accessToken->expires_at->toDateTimeString(),
        ]);
    }

    protected function checkingToken($request): bool
    {
        // Find token by bearer token
        $token = PersonalAccessToken::findToken($request->bearerToken());

        // Assign token to token instance
        $this->token = $token;

        // Check if token is valid and not expired
        return $token && ! ($token->expires_at && $token->expires_at->isPast());
    }

    protected function createAccessToken($publicKey): ?object
    {
        // Get merchant via public key and check if merchant exists and is approved
        $merchant = Merchant::where('public_key', $publicKey)
            ->where('status', MerchantStatus::Approved)
            ->first();

        // If merchant does not exist, return error
        if (! $merchant) {
            return null;
        }

        // Create access token and cache it for 30 minutes
        return cache()->remember('access-token-'.$publicKey, now()->addMinutes(30), function () use ($merchant) {
            return $merchant->createToken('access_token', ['*'], now()->addMinutes(30));
        });
    }

    protected function withError(string|array $message, int $statusCode = 402): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }

    protected function withSuccess(array $data): JsonResponse
    {
        return response()->json(array_merge(['status' => 'success'], $data));
    }
}
