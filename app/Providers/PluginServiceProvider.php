<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Remotelywork\Installer\Repository\App;

class PluginServiceProvider extends ServiceProvider
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
        if (App::dbConnectionCheck() && Schema::hasTable('plugins')) {
            // Nexmo/Vonage sms plugin
            if (plugin_active('Vonage')) {
                $VonageCredential = json_decode(plugin_active('Vonage')->data);
                config()->set([
                    'sms.connections.vonage.vonage_from' => $VonageCredential->from,
                    'sms.connections.vonage.api_key' => $VonageCredential->api_key,
                    'sms.connections.vonage.api_secret' => $VonageCredential->api_secret,
                ]);
            }

            // Twilio sms plugin
            if (plugin_active('Twilio')) {
                $twilioCredential = json_decode(plugin_active('Twilio')->data);
                config()->set([
                    'sms.connections.twilio.twilio_sid' => $twilioCredential->twilio_sid,
                    'sms.connections.twilio.twilio_auth_token' => $twilioCredential->twilio_auth_token,
                    'sms.connections.twilio.twilio_phone' => $twilioCredential->twilio_phone,
                ]);
            }

            // Pusher Notification plugin
            if (plugin_active('Pusher')) {
                $push_notification = plugin_active('Pusher');
                if ($push_notification->name == 'Pusher') {
                    $pusherCredential = json_decode($push_notification->data);
                    config()->set([
                        'broadcasting.default' => 'pusher',
                        'broadcasting.connections.pusher.app_id' => $pusherCredential->pusher_app_id,
                        'broadcasting.connections.pusher.key' => $pusherCredential->pusher_app_key,
                        'broadcasting.connections.pusher.secret' => $pusherCredential->pusher_app_secret,
                        'broadcasting.connections.pusher.options.cluster' => $pusherCredential->pusher_app_cluster,
                        'broadcasting.connections.pusher.options.host' => "api-{$pusherCredential->pusher_app_cluster}.pusher.com",
                    ]);
                }
            }

            // Flutterwave Plugin
            if (plugin_active('Flutterwave')) {
                $flutterwave = plugin_active('Flutterwave');
                if ($flutterwave->name == 'Flutterwave') {

                    $flutterwaveCredentials = json_decode($flutterwave->data);

                    config()->set([
                        'services.flutterwave.connections.secret_key' => $flutterwaveCredentials->secret_key,
                    ]);
                }
            }

            // Default plugin
            config()->set('sms.default', default_plugin('sms') ?? false);
        }
    }
}
