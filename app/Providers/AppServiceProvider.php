<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        // Notify
        $this->app->singleton('notify', function () {
            return new \App\Facades\Notification\Notify;
        });

        $this->configureAssetUrl();
    }

    public function configureAssetUrl(): void
    {
        $assetUrl = url('public');
        $this->app->singleton('url', function ($app) use ($assetUrl) {
            $routes = $app['router']->getRoutes();
            $request = $app['request'];
            $url = new UrlGenerator($routes, $request, $assetUrl);

            return $url;
        });
    }
}
