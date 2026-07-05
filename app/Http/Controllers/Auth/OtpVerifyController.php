<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtpVerifyController extends Controller
{
    use NotifyTrait;

    public function index()
    {
        return view('frontend::auth.otp');
    }

    public function resend()
    {
        $user = User::query()
            ->find(Auth::user()->id);
        $otp = random_int(1000, 9999);
        $shortcodes = [
            '[[otp_code]]' => $otp,
        ];
        $this->smsNotify('otp', $shortcodes, $user->phone);
        $user->update([
            'otp' => $otp,
        ]);

        return redirect()->back()->with('success', __('Otp Resend Successfully'));
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|array',
        ]);
        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->with('error', __('OTP Must be integer!'));
        }

        $otp = (int) implode('', $request->otp);
        $user = User::where('phone', $request->phone)->where('otp', $otp)->first();
        if ($user) {
            $user->phone_verified = true;
            $user->otp = null;
            $user->save();
            notify()->success(__('Otp Verified Successfully!!'));

            return redirect()->route('user.dashboard');
        }

        return redirect()->back()->with('error', 'OTP does not match');
    }

    public function adminVerify()
    {
        return view('backend.auth.two-verify');
    }

    public function adminVerifyPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $user = auth('admin')->user();
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->two_fa_secret, $request->otp);

        if (! $valid) {
            notify()->error('Invalid OTP');

            return redirect()->back();
        }

        session(['admin_two_fa_verified' => true]);

        return redirect()->intended(route('admin.dashboard'));
    }
}
