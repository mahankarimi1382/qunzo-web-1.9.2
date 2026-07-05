<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('app.demo')) {
            return $next($request);
        } elseif ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE') || $request->route()->getName() == 'admin.user.login') {

            notify()->warning(__('You cannot change anything in this demo version'), 'warning');

            return redirect()->back();
        }

        return $next($request);
    }
}
