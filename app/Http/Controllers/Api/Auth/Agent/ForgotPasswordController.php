<?php

namespace App\Http\Controllers\Api\Auth\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class ForgotPasswordController extends Controller
{
    use ApiResponseTrait, NotifyTrait;

    public function sendResetLinkEmail(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validation->fails()) {
            $error = makeValidationException($validation->errors()->all());

            return $this->error($error->getMessage(), 422, $validation->errors());
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
        ];

        $this->sendNotify($request->email, 'forgot_password_otp', 'User', $shortcodes, null, null, $token);

        return $this->successWithoutData('Password reset email sent');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'otp' => 'required|digits:6',
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->otp,
            ])
            ->first();

        if (! $updatePassword) {
            return $this->error('Invalid otp', 422, ['otp' => 'Invalid otp']);
        }

        return $this->success('OTP verified successfully');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->otp,
            ])
            ->first();

        if (! $updatePassword) {
            return $this->error('Invalid otp', 422, ['otp' => 'Invalid otp']);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully',
        ]);
    }
}
