<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

use function base_path;
use function class_exists;
use function is_array;
use function is_dir;
use function json_decode;

class AddonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerAddonProviders();
    }

    private function registerAddonProviders(): void
    {
        // Get active addons
        $addons = $this->getActiveAddons();
        foreach ($addons as $addon) {
            if (! empty($addon['provider']) && class_exists($addon['provider'])) {
                $this->app->register($addon['provider']);
            }
        }
    }

    private function getActiveAddons(): array
    {
        $addonsPath = base_path('modules/Addons');

        if (! is_dir($addonsPath)) {
            return [];
        }

        return collect(File::directories($addonsPath))
            ->map(function ($addonPath) {
                $pluginJsonPath = $addonPath.'/plugin.json';

                if (! File::exists($pluginJsonPath)) {
                    return null;
                }

                $data = json_decode(File::get($pluginJsonPath), true);

                if (! is_array($data) || empty($data['active'])) {
                    return null;
                }

                return [
                    'provider' => $data['provider'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
