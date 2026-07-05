<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    use ApiResponseTrait, NotifyTrait;

    public function sendVerifyEmail(Request $request, $user = null)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        if (! setting('email_verification', 'permission')) {
            return $this->successWithoutData('');
        }

        $token = random_int(100000, 999999);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $shortcodes = [
            '[[token]]' => $token,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
            '[[full_name]]' => str($request->email)->explode('@')->first(),
        ];

        $this->sendNotify($request->email, 'app_email_verification', 'User', $shortcodes, null, null, $token);

        $message = 'Verification email sent';

        if (config('app.demo')) {
            $message = 'Verification email sent, code : ' . $token;
        }

        return $this->successWithoutData($message);
    }

    public function validateVerifyEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validate->fails()) {
            return $this->error($validate->errors(), 422);
        }

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->otp,
            ])
            ->first();

        if (! $updatePassword) {
            return $this->error('Invalid otp', 422, ['otp' => 'Invalid otp']);
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return $this->successWithoutData('Email verified successfully');
    }

    public function webEmailVerify(Request $request, $id, $hash)
    {
        $user = auth()->user();

        if (! $user || ! hash_equals((string) $user->getKey(), (string) $id) || ! hash_equals((string) $user->getEmailForVerification(), (string) $hash)) {
            return $this->error('Invalid verification link', 422);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 422);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->successWithoutData('Email verified successfully');
    }

    public function sendWebEmailVerificationNotify(Request $request)
    {
        $user = auth()->user();

        $tokenUrl = url()->temporarySignedRoute(
            'api.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $shortcodes = [
            '[[token]]' => $tokenUrl,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[full_name]]' => $user->full_name,
            '[[site_url]]' => '#',
        ];

        $this->sendNotify($user->email, 'email_verification', 'User', $shortcodes, null, null, null);

        return $this->successWithoutData('Verification email sent');
    }
}
