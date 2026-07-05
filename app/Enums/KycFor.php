<?php

namespace App\Enums;

enum KycFor: string
{
    case User = 'user';
    case Agent = 'agent';
    case Merchant = 'merchant';
    case VerifiedTrader = 'verified_trader';

    public static function allOptions(): array
    {
        return array_filter(self::cases(), function ($case) {
            if ($case === self::VerifiedTrader && addonActive('p2p-trading') === false) {
                return false;
            }
            return true;
        });;
    }
}
