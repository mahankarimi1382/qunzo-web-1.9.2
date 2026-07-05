<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Remotelywork\Installer\Repository\App as MainApp;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! MainApp::dbConnectionCheck()) {
            return $next($request);
        }

        App::setLocale(session()->get('locale', defaultLocale()));

        return $next($request);
    }
}
