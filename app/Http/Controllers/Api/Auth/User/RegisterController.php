<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Enums\BoardingStep;
use App\Enums\KycFor;
use App\Enums\KYCStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\LoginActivities;
use App\Models\User;
use App\Services\RegisterService;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use ApiResponseTrait, NotifyTrait;

    public function __construct(
        private RegisterService $registerService
    ) {}

    private function checkRegistration(): bool
    {
        return (bool) setting('account_creation', 'permission');
    }

    public function register(Request $request)
    {
        if (! $this->checkRegistration()) {
            return $this->error(__('Registration is disabled'));
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'i_agree' => 'required',
            'is_email_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $exception = makeValidationException($validator->errors()->all());

            return $this->error($exception->getMessage(), 422, $exception->errors());
        }

        try {

            $tempUsername = str($request->email)->explode('@')->first();

            // Create user account
            DB::beginTransaction();

            $user = User::create([
                'first_name' => str($tempUsername)->limit(10)->value(),
                'last_name' => str($tempUsername)->limit(10)->value(),
                'username' => generateUniqueUsername($tempUsername),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kyc' => Kyc::where('for', KycFor::User)->where('status', true)->count() == 0 ? KYCStatus::Verified->value : KYCStatus::NOT_SUBMITTED->value,
                'role' => UserType::User,
                'current_step' => BoardingStep::PERSONAL_INFO,
                'email_verified_at' => $request->boolean('is_email_verified') ? now() : null,
            ]);

            LoginActivities::add();

            DB::commit();

            return $this->success([
                'token' => $user->createToken('auth_token', ['user'])->plainTextToken,
                'token_type' => 'Bearer',
            ], __('Registration successful!'));
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return $this->error(__('Sorry! Something went wrong.'));
        }
    }

    public function personalInfoUpdate(Request $request)
    {
        $usernameRequired = $this->registerService->isFieldRequired('username');
        $phoneRequired = $this->registerService->isFieldRequired('phone');
        $countryRequired = $this->registerService->isFieldRequired('country');
        $referralCodeRequired = $this->registerService->isFieldRequired('referral_code');
        $genderRequired = $this->registerService->isFieldRequired('gender');

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => [Rule::requiredIf($usernameRequired), 'string', 'alpha_num', 'max:255', 'unique:users,username,'.auth()->id()],
            'phone' => [Rule::requiredIf($phoneRequired), 'string', 'max:255', 'unique:users,phone,'.auth()->id()],
            'country' => [Rule::requiredIf($countryRequired), 'string', 'max:255'],
            'invite' => [Rule::requiredIf($referralCodeRequired), 'string', 'exists:users,referral_code'],
            'gender' => [Rule::requiredIf($genderRequired), 'string', 'in:male,female,other', 'max:255'],
        ]);

        if ($validator->fails()) {
            $exception = makeValidationException($validator->errors()->all());

            return $this->error($exception->getMessage(), 422, $exception->errors());
        }

        try {

            $referralUser = User::where('referral_code', $request->invite)->first();

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

            // Create user account
            DB::beginTransaction();

            $user = tap(User::find(auth()->id()), function ($user) use ($request, $username, $phone, $country) {
                $user->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'username' => $username,
                    'phone' => $phone,
                    'country' => $country,
                    'gender' => $request->gender,
                    'ref_id' => $referralUser->id ?? null,
                    'current_step' => BoardingStep::ID_VERIFICATION,
                ]);
            });

            $this->registerService->distributeSignUpBonus($user);

            if ($referralUser) {
                $this->registerService->processReferralBonus($referralUser, $user);
            }

            DB::commit();

            return $this->success([
                'user' => $user,
            ]);
        } catch (\Throwable $throwable) {
            DB::rollBack();

            Log::error('User personal info update error: '.$throwable->getMessage());

            return $this->error(__('Sorry! Something went wrong.'));
        }
    }
}
