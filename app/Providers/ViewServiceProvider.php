<?php

namespace App\Providers;

use App\Models\LandingPage;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Remotelywork\Installer\Repository\App;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (App::dbConnectionCheck() && ! app()->runningInConsole()) {
            View::composer(['backend.include.__side_nav', 'backend.setting.site_setting.include.__global'], function ($view) {
                $view->with([
                    'landingSections' => cache()->remember('landingSections', 60 * 60 * 24, function () {
                        return LandingPage::currentTheme()->where('locale', app()->getLocale())->whereNot('code', 'footer')->orderBy('sort')->get();
                    }),
                    'pages' => cache()->remember('pages', 60 * 60 * 24, function () {
                        return Page::currentTheme()->where('locale', app()->getLocale())->get();
                    }),
                ]);
            });

            View::composer(['*'], function ($view) {
                $view->with([
                    'currencySymbol' => setting('currency_symbol', 'global'),
                    'currency' => setting('site_currency', 'global'),
                    'languages' => once(fn () => Language::where('status', true)->get()),
                ]);
            });
        }
    }
}
