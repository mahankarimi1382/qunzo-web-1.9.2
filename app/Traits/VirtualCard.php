<?php

namespace App\Traits;

use Card\Stripe\StripeCard;

trait VirtualCard
{
    public function cardProviderMap($code)
    {
        $providers = [
            'stripe' => StripeCard::class,
        ];

        if (array_key_exists($code, $providers)) {
            return app($providers[$code]);
        }

        notify()->error(__('No provider found!'));

        return back();
    }
}
