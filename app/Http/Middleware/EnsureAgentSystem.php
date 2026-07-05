<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentSystem
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->expectsJson() && ! agentSystemEnabled()) {
            return $this->error(__('Agent system is not enabled'), 404);
        } elseif (! setting('agent_system', 'permission')) {
            abort(404);
        }

        return $next($request);
    }
}
