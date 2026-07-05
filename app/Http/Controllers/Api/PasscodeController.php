<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasscodeController extends Controller
{
    use ApiResponseTrait;

    public function passcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required|integer',
            'passcode_confirmation' => 'required|integer|same:passcode',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $user = $request->user();
        $user->passcode = bcrypt($request->passcode);
        $user->save();

        return $this->success(__('Passcode turned on'));
    }

    public function changePasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_passcode' => 'required',
            'passcode' => 'required|integer',
            'passcode_confirmation' => 'required|integer|same:passcode',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        if (! Hash::check($request->old_passcode, $user->passcode)) {
            return $this->error(__('Old Passcode is wrong!'), 422);
        }

        $user->passcode = bcrypt($request->passcode);
        $user->save();

        return $this->success(__('Passcode changed successfully'));
    }

    public function disablePasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        if (! Hash::check($request->password, $user->password)) {
            return $this->error(__('Password is wrong!'), 422);
        }

        $user->passcode = 0;
        $user->save();

        return $this->success(__('Passcode turned off'));
    }

    public function verifyPasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        if (! Hash::check($request->passcode, $request->user()->passcode)) {
            return $this->error(__('Passcode is wrong!'), 422);
        }

        return $this->success(__('Passcode verified successfully'));
    }
}
