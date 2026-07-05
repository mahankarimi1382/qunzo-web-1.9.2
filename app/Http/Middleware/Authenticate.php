<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Authenticate extends Middleware
{
    /**
     * The currently used guards.
     */
    protected array $guards = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Store guards for later use in redirection
        $this->guards = $guards;

        // Authenticate the request
        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Determine the guard being used
        $guard = Arr::first($this->guards) ?? 'web';

        // Redirect based on the guard
        return match ($guard) {
            'admin' => route('admin.login'),
            'merchant' => route('merchant.login'),
            'agent' => route('agent.login'),
            default => route('login'),
        };
    }
}
