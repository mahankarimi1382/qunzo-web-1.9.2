<?php

namespace Remotelywork\Installer\Repository;

use Illuminate\Support\Facades\Http;

class LicenseValidator
{
    protected static $token = 'RTlkZ240S1B0clJ4VXdkWjRuMDN0c2MzcWVzcXpCaU4=';

    public static function validate($licenseKey)
    {
        $repsonse = Http::withSensitiveToken(self::$token)
            ->withOptions([
                'verify' => false,
            ])
            ->get('https://api.envato.com/v3/market/author/sale', [
                'code' => $licenseKey,
            ]);

        return $repsonse;
    }
}
