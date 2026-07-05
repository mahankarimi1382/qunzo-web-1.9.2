<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Remotelywork\Installer\Repository\App;
use Symfony\Component\HttpFoundation\Response;

class IsMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (! App::dbConnectionCheck()) {
            return $next($request);
        }

        $isMaintenanceEnabled = (bool) setting('maintenance_mode', 'site_maintenance');

        if (app()->isDownForMaintenance() && ! $isMaintenanceEnabled) {
            Artisan::call('up');
        } else {
            if ($isMaintenanceEnabled) {
                $artisan = 'down --secret='.'"'.setting('secret_key', 'site_maintenance').'"';
                Artisan::call($artisan);
            }
        }

        return $next($request);
    }
}
