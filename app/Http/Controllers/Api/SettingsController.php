<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class SettingsController extends Controller
{
    use ApiResponseTrait, ImageUpload;

    public function profile()
    {
        $user = request()->user();

        return $this->success($user, __('Profile data'));
    }

    public function profileUpdate(Request $request)
    {
        $user = request()->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'username' => 'required|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'gender' => 'required',
            'date_of_birth' => 'date',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $input = $request->all();

        if ($request->hasFile('avatar')) {
            $input['avatar'] = self::imageUploadTrait($request->avatar, $user->avatar);
        }

        try {
            $user->update($input);
        } catch (\Throwable $th) {
            return $this->error(__('Sorry! Something went wrong.'), 500, $request->all());
        }

        return $this->success(null, __('Profile updated successfully'));
    }

    public function twoFa(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'one_time_password' => [
                Rule::requiredIf($type === 'enable'),
            ],
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = request()->user();
        if ($type == 'enable') {
            session([
                config('google2fa.session_var') => [
                    'auth_passed' => false,
                ],
            ]);

            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->isAuthenticated()) {

                $user->update([
                    'two_fa' => 1,
                ]);

                return $this->successWithoutData(__('2FA enabled successfully'));
            }

            return $this->error(__('One time key is wrong!'), 422);
        } elseif ($type == 'disable') {

            if (Hash::check(request('one_time_password'), $user->password)) {
                $user->update([
                    'two_fa' => 0,
                ]);

                return $this->successWithoutData(__('2FA disabled successfully'));
            }

            return $this->error(__('Your password is wrong!'), 422);
        } elseif ($type == 'generate') {
            $google2fa = app('pragmarx.google2fa');
            $secret = $google2fa->generateSecretKey();

            $user->update([
                'google2fa_secret' => $secret,
            ]);

            return $this->success([
                'qr_code' => $google2fa->getQRCodeInline(setting('site_title', 'global'), $user->email, $secret),
                'secret' => $secret,
            ], __('QR Code and Secret Key generate successfully'));
        }

        return $this->error(__('Invalid request'), 422);
    }

    public function accountClose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = request()->user();
        $user->update([
            'status' => 2,
            'close_reason' => $request->reason,
        ]);

        return $this->successWithoutData(__('Your account is closed successfully'));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = request()->user();
        $user->password = \Hash::make($request->password);
        $user->save();

        return $this->successWithoutData(__('Password changed successfully'));
    }
}
