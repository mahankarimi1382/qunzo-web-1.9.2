<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifyTrait;

class RegisterService
{
    use NotifyTrait;

    public function distributeSignUpBonus($user)
    {
        if (! setting('referral_signup_bonus', 'permission') || (float) setting('signup_bonus', 'fee') <= 0) {
            return;
        }

        $alreadyGiven = Transaction::where('user_id', $user->id)
            ->where('type', TxnType::SignupBonus)
            ->exists();

        if ($alreadyGiven) {
            return;
        }

        $signupBonus = (float) setting('signup_bonus', 'fee');
        $user->increment('balance', $signupBonus);
        (new Txn)->new($signupBonus, 0, $signupBonus, 'default', 'system', 'Signup Bonus', TxnType::SignupBonus, TxnStatus::Success, null, null, $user->id);
        session()->flash('sign_up_bonus', setting('currency_symbol', 'global') . $signupBonus);
    }

    public function processReferralBonus($referral, $user)
    {
        $email_verification = setting('email_verification', 'permission') ? $referral->email_verified_at !== null : true;

        // Sign Up Referral Bonus
        if (setting('sign_up_referral', 'permission') && $email_verification) {

            $referralBonus = (float) setting('referral_bonus', 'fee');
            // User who was sharing link
            $provider = $referral;
            $provider->increment('balance', $referralBonus);

            Transaction::create([
                'user_id' => $provider->id,
                'from_user_id' => $user->id,
                'from_model' => 'User',
                'wallet_type' => 'default',
                'description' => 'Referral Bonus via ' . $user->full_name,
                'type' => TxnType::Referral,
                'amount' => $referralBonus,
                'charge' => 0,
                'final_amount' => $referralBonus,
                'method' => 'System',
                'status' => TxnStatus::Success,
            ]);

            $shortcodes = [
                '[[full_name]]' => $provider->full_name,
                '[[referred_name]]' => $user->full_name,
                '[[referred_account_no]]' => $user->account_number,
                '[[joined_at]]' => $user->created_at,
                '[[referral_link]]' => '#',
                '[[site_title]]' => setting('site_title', 'global'),
            ];

            $this->sendNotify($provider->email, 'user_referral_join', 'User', $shortcodes, $provider->phone, $provider->id, '#');
        }
    }

    public function isFieldRequired($field)
    {
        return getPageSetting("{$field}_show") && getPageSetting("{$field}_validation");
    }
}
