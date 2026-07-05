<?php

namespace App\Http\Controllers\Api\Auth\Agent;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (! $user->two_fa) {
            return $this->error('2FA is not enabled', 422);
        }

        if (! $user->google2fa_secret || strlen($user->google2fa_secret) < 16) {
            return $this->error('Enable 2FA from security settings.', 422);
        }

        // Check code is valid
        if (! @$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return $this->error('Invalid code', 422);
        }

        return $this->success('Code verified successfully');
    }
}
