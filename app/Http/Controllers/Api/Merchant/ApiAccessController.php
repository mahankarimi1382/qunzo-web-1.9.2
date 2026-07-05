<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;

class ApiAccessController extends Controller
{
    public function getAccessKeys()
    {
        $user = request()->user()->merchant;

        return response()->json([
            'status' => true,
            'data' => [
                'public_key' => $user->public_key,
                'secret_key' => $user->secret_key,
            ],
        ]);
    }

    public function regenerateAccessKey()
    {
        $user = request()->user()->merchant;
        $user->public_key = generateKey('public_key');
        $user->secret_key = generateKey('secret_key');
        $user->save();

        return response()->json([
            'status' => true,
            'data' => [
                'public_key' => $user->public_key,
                'secret_key' => $user->secret_key,
            ],
        ]);
    }
}
