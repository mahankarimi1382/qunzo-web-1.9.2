<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Symfony\Component\HttpFoundation\Response;

class TwoFaCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! setting('fa_verification', 'permission') || ! $request->user()->two_fa) {
            return $next($request);
        }

        $requestsForAuthenticatate = $request;

        session([
            'redirect_to' => $this->redirectTo($request),
        ]);

        $requestsForAuthenticatate['one_time_password'] = is_array($requestsForAuthenticatate['one_time_password']) ? collect($request->get('one_time_password'))->implode('') : $request->get('one_time_password');

        $authenticator = app(Authenticator::class)->boot($requestsForAuthenticatate);
        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }

    protected function redirectTo($request)
    {
        return match (true) {
            str_contains($request->path(), 'merchant') => route('merchant.2fa.verify'),
            str_contains($request->path(), 'agent') => route('agent.2fa.verify'),
            default => route('user.2fa.verify'),
        };
    }
}
