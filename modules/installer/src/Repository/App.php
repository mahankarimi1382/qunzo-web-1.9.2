<?php

namespace Remotelywork\Installer\Repository;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class App
{
    protected static $cacheKey = 'license_validated';

    protected static $pluginCacheKey = 'plugin_license_validated_';

    protected static $validatedTtl = 86400; // 24 hours

    protected static $installedFile = 'installed';

    public static function dbConnectionCheck(): bool
    {
        try {

            DB::getPdo();
            DB::connection()->getDatabaseName();

            $ok = file_exists(storage_path(self::$installedFile));

            return $ok;
        } catch (\Throwable $throwable) {
            $ok = false;
        }

        return $ok;
    }

    public static function initApp()
    {
        return self::validateLicense();
    }

    public static function validateLicense($code = null)
    {
        $code ??= config('app.license_key');

        if (Cache::has(self::$cacheKey)) {
            return true;
        }

        $repsonse = LicenseValidator::validate($code);

        if ($repsonse->successful()) {
            Cache::put(self::$cacheKey, true, self::$validatedTtl);

            return true;
        }

        Cache::forget(self::$cacheKey);

        return false;
    }

    public static function validateAddonLicense(?string $pluginSlug, string $code)
    {
        $cacheKey = self::$pluginCacheKey.$pluginSlug;

        if (Cache::has($cacheKey)) {
            return true;
        }

        $repsonse = LicenseValidator::validate($code);

        if ($repsonse->successful()) {
            Cache::put($cacheKey, true, self::$validatedTtl);

            return true;
        }

        Cache::forget($cacheKey);

        return false;
    }

    public static function forgetLicenseCache($pluginSlug = null)
    {
        Cache::forget(self::$pluginCacheKey.$pluginSlug);
    }
}
