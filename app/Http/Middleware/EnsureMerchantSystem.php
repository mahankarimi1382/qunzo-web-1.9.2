<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchantSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->expectsJson() && ! setting('merchant_system', 'permission')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant system is not enabled',
            ], 404);
        } elseif (! setting('merchant_system', 'permission')) {
            abort(404);
        }

        return $next($request);
    }
}
