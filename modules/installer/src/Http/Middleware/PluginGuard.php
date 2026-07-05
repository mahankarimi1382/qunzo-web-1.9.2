<?php

namespace Remotelywork\Installer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;
use Remotelywork\Installer\Repository\App;

class PluginGuard
{
    public function handle($request, Closure $next, $pluginSlug)
    {
        $pluginJson = $this->getPluginJson($pluginSlug);
        if ($pluginJson['active'] == false) {
            abort(404);
        }

        $licenseKey = $pluginJson['license_key'];

        $response = App::validateAddonLicense($pluginSlug, $licenseKey);

        if ($response === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plugin is not activated or license key is invalid.',
            ], 403);
        }

        return $next($request);
    }

    private function getPluginJson($slug)
    {
        $addonsPath = base_path('modules/Addons');

        if (! is_dir($addonsPath)) {
            return null;
        }

        $directories = File::directories($addonsPath);

        $data = collect($directories)
            ->reject(fn($addonPath) => ! File::exists($addonPath . '/plugin.json'))
            ->map(function ($addonPath) use ($slug) {
                $pluginJsonPath = $addonPath . '/plugin.json';

                $data = json_decode(File::get($pluginJsonPath), true) ?: [];

                if (! is_array($data) || $data['slug'] !== $slug) {
                    return null;
                }

                return $data;
            })
            ->filter()
            ->first();

        return $data;
    }

    public static function virtualCards(): string
    {
        return 'plugin_guard:virtual-cards';
    }

    public static function giftCards(): string
    {
        return 'plugin_guard:gift-cards';
    }
}
