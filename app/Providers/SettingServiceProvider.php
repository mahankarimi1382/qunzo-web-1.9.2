<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Remotelywork\Installer\Repository\App;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (App::dbConnectionCheck() && Schema::hasTable('settings')) {

            $timezone = setting('site_timezone', 'global');

            config()->set([
                'mail.from.name' => setting('email_from_name', 'mail'),
                'mail.from.address' => setting('email_from_address', 'mail'),
                'mail.mailers.smtp.host' => setting('mail_host', 'mail'),
                'mail.mailers.smtp.port' => setting('mail_port', 'mail'),
                'mail.mailers.smtp.encryption' => setting('mail_secure', 'mail'),
                'mail.mailers.smtp.username' => setting('mail_username', 'mail'),
                'mail.mailers.smtp.password' => setting('mail_password', 'mail'),
                'app.debug' => setting('debug_mode', 'permission'),
                'debugbar.enabled' => setting('debug_mode', 'permission'),
                'app.timezone' => $timezone,
                'session.lifetime' => setting('session_lifetime', 'system'),
            ]);

            date_default_timezone_set($timezone);
        }
    }
}
