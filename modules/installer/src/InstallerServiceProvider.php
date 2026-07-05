<?php

namespace Remotelywork\Installer;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Remotelywork\Installer\Http\Middleware\InstallCheck;
use Remotelywork\Installer\Http\Middleware\IsInstalled;
use Remotelywork\Installer\Http\Middleware\PluginGuard;
use Remotelywork\Installer\Http\Middleware\ValidateLicense;

class InstallerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('is_installed', IsInstalled::class);
        $router->aliasMiddleware('install_check', InstallCheck::class);
        $router->aliasMiddleware('trans', ValidateLicense::class);
        $router->aliasMiddleware('plugin_guard', PluginGuard::class);

        $this->publishes([
            __DIR__.'/assets' => public_path('global/installer'),
        ], 'installer-assets');

        $this->loadViewsFrom(__DIR__.'/views', 'installer');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->mergeConfigFrom(__DIR__.'/config/installer.php', 'installer');

        // Http macro
        Http::macro('withSensitiveToken', function ($token) {
            return Http::withToken(base64_decode($token));
        });
    }
}
