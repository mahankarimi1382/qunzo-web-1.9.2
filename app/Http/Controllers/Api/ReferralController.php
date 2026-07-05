<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DirectReferralsResource;
use App\Http\Resources\ReferralTreeResource;
use App\Traits\ApiResponseTrait;

class ReferralController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $currencySymbol = setting('currency_symbol', 'global');
        $user = auth()->user();

        return $this->success([
            'amount' => $currencySymbol.setting('referral_bonus', 'fee'),
            'code' => $user->referral_code,
            'referral_count' => $user->referrals()->count(),
            'is_shown_referral_rules' => (bool) setting('referral_rules_visibility'),
            'rules' => json_decode(setting('referral_rules')),
        ], __('Referral information'));
    }

    public function directReferrals()
    {
        $users = auth()->user()->referrals()->get();
        $users = DirectReferralsResource::collection($users);

        return $this->success($users, __('Direct referrals'));
    }

    public function referralTree()
    {
        $user = auth()->user();
        $users = $user->load('referralTree');

        return $this->success(new ReferralTreeResource($users), __('Referral tree'));
    }
}
