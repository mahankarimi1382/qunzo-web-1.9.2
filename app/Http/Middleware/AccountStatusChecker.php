<?php

namespace App\Http\Middleware;

use App\Enums\BoardingStep;
use App\Enums\KYCStatus;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountStatusChecker
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($user->kyc != KYCStatus::Verified->value) {
                $this->markAsKycUnverified($user);
                $this->logoutUser($user);
                return $this->error('Your KYC verification failed. Please resubmit your KYC documents.', 401);
            }

            if (!$user->status) {
                $this->logoutUser($user);
                return $this->error('Your account is deactivated. Please contact support.', 401);
            }
        }

        return $next($request);
    }

    private function markAsKycUnverified($user)
    {
        $user->update([
            'kyc' => KYCStatus::NOT_SUBMITTED,
            'current_step' => BoardingStep::ID_VERIFICATION,
        ]);
    }

    private function logoutUser($user)
    {
        $user->tokens()->delete();
    }
}
