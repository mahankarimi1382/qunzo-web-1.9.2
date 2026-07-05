<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Enums\BoardingStep;
use App\Enums\KYCStatus;
use App\Http\Controllers\Controller;
use App\Models\LoginActivities;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validate->fails()) {
            return $this->error($validate->errors(), 422);
        }

        $email = $request->get('email');
        $type = ! $this->isEmail($email) ? 'username' : 'email';

        // Get the user by email or username
        $column = $type === 'email' ? 'email' : 'username';
        $user = User::where($column, $email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($this->throttleKey($request->email));

            return $this->error(__('auth.failed'), 422);
        }

        if ($user->status !== 1) {
            return $this->error(__('Your account is inactive. Please contact our support'), 403);
        }

        $this->ensureIsNotRateLimited($type, $request);

        RateLimiter::clear($this->throttleKey($request->email));

        $token = $user->createToken('auth_token', ['user'])->plainTextToken;

        LoginActivities::add(id: $user->id);

        return $this->success(['token' => $token], 'Logged in successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    private function isEmail($param)
    {
        return filter_var($param, FILTER_VALIDATE_EMAIL);
    }

    public function throttleKey($email)
    {
        return Str::transliterate(Str::lower($email) . '|' . request()->ip());
    }

    public function ensureIsNotRateLimited($type, Request $request)
    {
        $throttleKey = $this->throttleKey($request->email);
        if (! RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            $type => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        $currentStep = $user->current_step;

        $steps = [
            BoardingStep::EMAIL_VERIFICATION->value => $user->email_verified_at !== null,
            BoardingStep::PASSWORD_SETUP->value => true,
            BoardingStep::PERSONAL_INFO->value => in_array($currentStep, [BoardingStep::ID_VERIFICATION, BoardingStep::COMPLETED]),
            BoardingStep::ID_VERIFICATION->value => $currentStep === BoardingStep::COMPLETED,
            BoardingStep::COMPLETED->value => $currentStep === BoardingStep::COMPLETED,
        ];

        $userData = $user->makeVisibleIf($request->google2fa_secret, 'google2fa_secret');
        $userData['boarding_steps'] = $steps;
        $userData['greetings'] = greeting();
        $userData['is_rejected'] = $user->kyc == KYCStatus::Failed->value;
        $userData['rejection_reason'] = $user->rejectedKycs()->latest()->first()?->message;
        $userData['addons'] = [
            'virtual_cards' => addonActive('virtual-cards'),
            'gift_cards' => addonActive('gift-cards'),
            'p2p_trading' => addonActive('p2p-trading'),
        ];

        return $this->success($userData, 'User Data fetch successfully');
    }
}
