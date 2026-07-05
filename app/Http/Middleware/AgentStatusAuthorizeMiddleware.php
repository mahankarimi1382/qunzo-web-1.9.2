<?php

namespace App\Http\Middleware;

use App\Enums\AgentStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AgentStatusAuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->route()->getName() == 'agent.dashboard' || $request->route()->getName() == 'agent.reSubmitRequest' || $request->route()->getName() == 'agent.reSubmitForm') {
            return $next($request);
        }

        if (Auth::guard('agent')->user()?->agent?->status == AgentStatus::Approved) {
            return $next($request);
        }

        $message = '';

        if (Auth::guard('agent')->user()?->agent?->status == AgentStatus::Rejected) {
            $message = __('Your request is rejected. Please provide valid information for approval.');
        } else {
            $message = __('Your request is pending. Please wait for approval.');
        }

        notify()->success($message);

        return redirect()->route('agent.dashboard');
    }
}
