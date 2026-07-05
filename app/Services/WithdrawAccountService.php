<?php

namespace App\Services;

use App\Models\UserWallet;
use App\Models\WithdrawAccount;
use App\Models\WithdrawMethod;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WithdrawAccountService
{
    use ImageUpload, NotifyTrait;

    public function checkAvailability($user)
    {
        if (! setting('user_withdraw', 'permission')) {
            return makeValidationException([
                'withdraw' => [__('Withdraw feature is disabled')],
            ]);
        }

        if (! $user->withdraw_status) {
            return makeValidationException([
                'withdraw' => [__('Your withdraw access is disabled')],
            ]);
        }

        if (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc' => [__('Please verify your KYC to use withdraw feature')],
            ]);
        }

        return true;
    }

    public function validateCreate(Request $request)
    {
        if (! setting('user_withdraw', 'permission')) {
            return makeValidationException([
                'withdraw' => [__('Withdraw feature is not enabled!')],
            ]);
        }

        $isMultiWalletEnabled = setting('multiple_currency', 'permission');

        $validator = Validator::make($request->all(), [
            'withdraw_method_id' => 'required|exists:withdraw_methods,id',
            'method_name' => 'required|string|max:255',
            'wallet_id' => Rule::requiredIf($isMultiWalletEnabled),
            'credentials' => 'nullable|array',
        ], [
            'withdraw_method_id.exists' => __('Selected withdraw method does not exist'),
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        $withdrawMethod = WithdrawMethod::where('id', $request->withdraw_method_id)
            ->where('status', true)
            ->first();

        if (! $withdrawMethod) {
            return makeValidationException([
                'withdraw_method_id' => [__('Selected withdraw method is not available')],
            ]);
        }
        $credentialsValidation = $this->validateCredentials($request->credentials, $withdrawMethod);

        if (isValidationException($credentialsValidation)) {
            return $credentialsValidation;
        }

        if ($request->wallet_id && $request->wallet_id !== 'default') {
            $wallet = UserWallet::where('user_id', auth()->id())
                ->where('id', $request->wallet_id)
                ->exists();

            if (! $wallet) {
                return makeValidationException([
                    'wallet_id' => [__('Selected wallet not found')],
                ]);
            }
        }

        return true;
    }

    public function validateUpdate(Request $request, $id)
    {
        $user = $request->user();

        if (setting('kyc_withdraw', 'permission') && ! $user->kyc && ! $user->agent && ! $user->merchant) {
            return makeValidationException([
                'kyc' => [__('Please verify your KYC to update withdraw account')],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'method_name' => 'required|string|max:255',
            'credentials' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }
        $withdrawAccount = WithdrawAccount::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $withdrawAccount) {
            return makeValidationException([
                'account' => [__('Withdraw account not found')],
            ]);
        }

        $withdrawMethod = WithdrawMethod::where('id', $withdrawAccount->withdraw_method_id)
            ->where('status', true)
            ->first();

        if (! $withdrawMethod) {
            return makeValidationException([
                'withdraw_method_id' => [__('Selected withdraw method is not available')],
            ]);
        }

        $credentialsValidation = $this->validateCredentials($request->credentials, $withdrawMethod, json_decode($withdrawAccount->credentials, true) ?? []);

        if (isValidationException($credentialsValidation)) {
            return $credentialsValidation;
        }

        return $withdrawAccount;
    }

    public function createAccount(Request $request)
    {
        $user = $request->user();
        $isApi = $request->expectsJson();

        try {

            $availabilityCheck = $this->checkAvailability($user);
            if (isValidationException($availabilityCheck)) {
                return $availabilityCheck;
            }

            $validation = $this->validateCreate($request);
            if (isValidationException($validation)) {
                return $validation;
            }

            DB::beginTransaction();

            $credentials = $request->credentials;

            foreach ($credentials as $key => $formData) {
                if (isset($formData['value']) && $formData['type'] == 'file' && $formData['value'] instanceof \Illuminate\Http\UploadedFile) {
                    $credentials[$key]['value'] = self::imageUploadTrait($formData['value']);
                }
            }

            $withdrawAccount = WithdrawAccount::create([
                'user_id' => $user->id,
                'user_wallet_id' => $request->wallet_id === 'default' ? 0 : $request->wallet_id,
                'withdraw_method_id' => $request->withdraw_method_id,
                'method_name' => $request->method_name,
                'credentials' => json_encode($credentials),
            ]);

            DB::commit();

            $this->sendAccountCreatedNotification($user, $withdrawAccount);

            return $withdrawAccount;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return makeValidationException([
                    'message' => [$e->getMessage()],
                ]);
            }

            throw $e;
        }
    }

    public function updateAccount(Request $request, $id)
    {
        $user = request()->user();
        $isApi = $request->expectsJson();

        try {

            $withdrawAccount = $this->validateUpdate($request, $id);
            if (isValidationException($withdrawAccount)) {
                return $withdrawAccount;
            }

            DB::beginTransaction();

            $oldCredentials = json_decode($withdrawAccount->credentials, true) ?? [];
            $credentials = $request->credentials;

            foreach ($credentials as $key => $formData) {
                if (isset($formData['value']) && $formData['type'] == 'file' && $formData['value'] instanceof \Illuminate\Http\UploadedFile) {
                    $credentials[$key]['value'] = self::imageUploadTrait($formData['value'], $oldCredentials[$key]['value'] ?? null);
                }
            }

            $withdrawAccount->update([
                'method_name' => $request->method_name,
                'credentials' => json_encode($credentials),
            ]);

            DB::commit();

            $this->sendAccountUpdatedNotification($user, $withdrawAccount);

            return $withdrawAccount;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return makeValidationException([
                    'message' => [$e->getMessage()],
                ]);
            }

            throw $e;
        }
    }

    public function deleteAccount($id)
    {
        $user = request()->user();

        try {
            $withdrawAccount = WithdrawAccount::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (! $withdrawAccount) {
                return makeValidationException([
                    'account' => [__('Withdraw account not found')],
                ]);
            }

            DB::beginTransaction();

            $oldCredentials = json_decode($withdrawAccount->credentials, true) ?? [];
            foreach ($oldCredentials as $credential) {
                if (
                    isset($credential['type'], $credential['value']) &&
                    $credential['type'] === 'file' &&
                    $credential['value']
                ) {
                    $this->fileDelete($credential['value']);
                }
            }

            $withdrawAccount->delete();

            DB::commit();

            $this->sendAccountDeletedNotification($user, $withdrawAccount);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAccounts($request)
    {
        $user = request()->user();

        $query = WithdrawAccount::query()
            ->with(['method', 'wallet.currency'])
            ->where('user_id', $user->id)
            ->when($request->keyword, fn ($q) => $q->where('method_name', 'like', '%'.$request->keyword.'%'))
            ->when($request->method_id, fn ($q) => $q->where('withdraw_method_id', $request->method_id))
            ->when($request->wallet_id && $request->wallet_id !== 'all', function ($q) use ($request) {
                $q->where('user_wallet_id', $request->wallet_id === 'default' ? 0 : $request->wallet_id);
            });

        return $query->latest();
    }

    public function getWithdrawMethods($currency = null)
    {
        $query = WithdrawMethod::where('status', true);

        if ($currency) {
            $currencyCode = $currency === 'default'
                ? setting('site_currency', 'global')
                : UserWallet::find($currency)?->currency?->code;

            $query->where('currency', $currencyCode);
        }

        return $query->get();
    }

    public function validateCredentials($credentials, $withdrawMethod, $oldCredentials = [])
    {
        foreach (json_decode($withdrawMethod->fields, true) ?? [] as $key => $field) {
            if ($field['validation'] == 'required' && (! isset($credentials[$field['name']])
                || empty($credentials[$field['name']])) && ! isset($oldCredentials[$field['name']])) {
                return makeValidationException([
                    'credentials.'.$field['name'] => [__('The :field is required.', ['field' => $field['name']])],
                ]);
            }
        }

        return true;
    }

    private function sendAccountCreatedNotification($user, $withdrawAccount)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[method_name]]' => $withdrawAccount->method_name,
            '[[created_at]]' => $withdrawAccount->created_at->format('Y-m-d H:i:s'),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $user->email,
            'withdraw_account_created',
            'User',
            $shortcodes,
            $user->phone,
            $user->id,
            Route::has('user.withdraw.account.index') ? route('user.withdraw.account.index') : ''
        );
    }

    private function sendAccountUpdatedNotification($user, $withdrawAccount)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[method_name]]' => $withdrawAccount->method_name,
            '[[updated_at]]' => $withdrawAccount->updated_at->format('Y-m-d H:i:s'),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $user->email,
            'withdraw_account_updated',
            'User',
            $shortcodes,
            $user->phone,
            $user->id,
            Route::has('user.withdraw.account.index') ? route('user.withdraw.account.index') : ''
        );
    }

    private function sendAccountDeletedNotification($user, $withdrawAccount)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[method_name]]' => $withdrawAccount->method_name,
            '[[deleted_at]]' => now()->format('Y-m-d H:i:s'),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $user->email,
            'withdraw_account_deleted',
            'User',
            $shortcodes,
            $user->phone,
            $user->id,
            Route::has('user.withdraw.account.index') ? route('user.withdraw.account.index') : ''
        );
    }
}
