<?php

namespace App\Http\Controllers\Api\Auth\Merchant;

use App\Enums\BoardingStep;
use App\Enums\KycFor;
use App\Enums\KYCStatus;
use App\Enums\MerchantStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\LoginActivities;
use App\Models\Merchant;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MerchantAuthenticationController extends Controller
{
    use ApiResponseTrait, ImageUpload, NotifyTrait;

    public function config()
    {
        return $this->success([
            'settings' => [
                'auto_approval' => ! setting('agent_verification', 'permission'),
            ],
        ], __('Agent registration configuration'));
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load('merchant');
            $currentStep = $user->current_step;
            $steps = [
                BoardingStep::EMAIL_VERIFICATION->value => $user->email_verified_at !== null,
                BoardingStep::PASSWORD_SETUP->value => true,
                BoardingStep::PERSONAL_INFO->value => in_array($currentStep, [BoardingStep::ID_VERIFICATION, BoardingStep::COMPLETED]),
                BoardingStep::ID_VERIFICATION->value => $currentStep === BoardingStep::COMPLETED,
                BoardingStep::COMPLETED->value => $currentStep === BoardingStep::COMPLETED,
            ];

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'gender' => $user->gender,
                    'role' => $user->role,
                    'kyc' => $user->kyc,
                    'merchant' => [
                        'status' => $user->merchant?->status,
                        'is_rejected' => $user->merchant?->status == MerchantStatus::Rejected,
                        'rejection_reason' => $user->merchant?->status == MerchantStatus::Rejected ? $user->rejectedKycs()->latest()->first()?->message : null,
                    ],
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'email_verified_at' => $user->email_verified_at,
                    'google2fa_secret' => $user->google2fa_secret ? true : false,
                    'two_fa' => $user->two_fa,
                    'avatar' => $user->avatar_path,
                    'account_number' => $user->account_number,
                    'city' => $user->city,
                    'zip_code' => $user->zip_code,
                    'address' => $user->address,
                    'date_of_birth' => $user->date_of_birth,
                    'boarding_steps' => $steps,
                    'greetings' => greeting(),
                    'unread_notifications_count' => $user->notifications()
                        ->where('for', 'merchant')
                        ->where('read', 0)->count(),
                ],
            ], __('Merchant profile details'));
        } catch (\Throwable $e) {
            return $this->error(__('Sorry! Something went wrong'), 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'email.exists' => __('Merchant not found in our records'),
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $user = User::query()
                ->with('merchant')
                ->has('merchant')
                ->where('role', UserType::Merchant)
                ->where('email', $request->email)
                ->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->error(__('auth.failed'), 400);
            }

            if ($user->merchant?->status == MerchantStatus::Disabled) {
                return $this->error(__('Your account is disabled. Please contact our support'), 403);
            }

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent();
            $token = $user->createToken($deviceName, ['merchant'])->plainTextToken;

            // Log activity
            LoginActivities::add('merchant');

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'role' => $user->role,
                    'merchant_status' => $user->merchant?->status,
                    'verification_required' => setting('merchant_verification', 'permission') && $user->merchant?->status == MerchantStatus::Pending,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], __('Login successful'));
        } catch (\Throwable $e) {
            return $this->error(__('Sorry! Something went wrong. Please try again'), 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'i_agree' => ['required', 'accepted'],
            'is_email_verified' => 'required|boolean',
        ], [
            'email.unique' => __('This email is already registered'),
            'password.confirmed' => __('Password confirmation does not match'),
            'i_agree.required' => __('You must agree to the terms and conditions'),
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $tempUsername = str($request->email)->explode('@')->first();

            // Create user account
            $user = User::create([
                'role' => UserType::Merchant,
                'first_name' => str($tempUsername)->limit(10)->value(),
                'last_name' => str($tempUsername)->limit(10)->value(),
                'username' => generateUniqueUsername($tempUsername),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kyc' => Kyc::where('for', KycFor::Merchant)->where('status', true)->count() == 0 ? KYCStatus::Verified->value : KYCStatus::NOT_SUBMITTED->value,
                'current_step' => BoardingStep::PERSONAL_INFO,
                'email_verified_at' => $request->boolean('is_email_verified') ? now() : null,
            ]);

            // Create merchant records
            Merchant::create([
                'user_id' => $user->id,
                'status' => ! setting('merchant_verification', 'permission')
                    ? MerchantStatus::Approved
                    : MerchantStatus::Pending,
            ]);

            // Create token
            $token = $user->createToken('merchant_auth_token')->plainTextToken;

            // Log activity
            LoginActivities::add('merchant');

            DB::commit();

            return $this->success([
                'token' => $token,
                'token_type' => 'Bearer',
            ], __('Merchant registration completed successfully'));
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Merchant register error: '.$e->getMessage());

            return $this->error(__('Sorry! Something went wrong.'), 500);
        }
    }

    public function personalInfoUpdate(Request $request)
    {
        $usernameRequired = $this->isFieldRequired('merchant_username');
        $phoneRequired = $this->isFieldRequired('merchant_phone');
        $countryRequired = $this->isFieldRequired('merchant_country');
        $genderRequired = $this->isFieldRequired('merchant_gender');

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'min:1', 'max:30', 'string'],
            'last_name' => ['required', 'min:1', 'max:30', 'string'],
            'username' => [Rule::requiredIf($usernameRequired), 'string', 'alpha_num', 'max:255', 'unique:users,username,'.auth()->id()],
            'phone' => [Rule::requiredIf($phoneRequired), 'string', 'max:255', 'unique:users,phone,'.auth()->id()],
            'country' => [Rule::requiredIf($countryRequired), 'string', 'max:255'],
            'gender' => [Rule::requiredIf($genderRequired), 'string', 'in:male,female,other', 'max:255'],
        ], [
            'first_name.required' => __('First name is required'),
            'last_name.required' => __('Last name is required'),
            'username.unique' => __('This username is already taken'),
            'phone.unique' => __('This phone number is already registered'),
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $username = ! $request->filled('username') ? generateUniqueUsername(trim($request->first_name.' '.$request->last_name)) : $request->username;

            if (! $request->filled('country')) {
                // Get Location
                $location = getLocation();
                $request->merge([
                    'country' => $location->name.':'.$location->dial_code,
                ]);
            }

            // Get dial code from country
            [$dial_code, $country] = explode(':', $request->country);

            $phone = $request->phone;

            $user = tap(User::find(auth()->id()), function ($user) use ($request, $username, $phone, $country) {
                $user->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'username' => $username,
                    'phone' => $phone,
                    'country' => $country,
                    'gender' => $request->gender,
                    'current_step' => BoardingStep::ID_VERIFICATION,
                ]);
            });

            DB::commit();

            return $this->success([], __('Profile info updated successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Merchant personal info update error: '.$e->getMessage());

            return $this->error(__('Sorry! Something went wrong.'), 500);
        }
    }

    private function isFieldRequired($field)
    {
        return getPageSetting("{$field}_show") && getPageSetting("{$field}_validation");
    }

    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return $this->success([], __('You have been logged out successfully.'));
        } catch (\Throwable $e) {
            return $this->error(__('Sorry! Something went wrong.'), 500);
        }
    }
}
