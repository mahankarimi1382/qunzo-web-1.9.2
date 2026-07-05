<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantActivationChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::guard('merchant')->check() && Auth::guard('merchant')->user()?->merchant?->status != \App\Enums\MerchantStatus::Approved) {
            notify()->error(__('Your merchant account is not approved yet'));

            return back();
        }

        return $next($request);
    }
}
