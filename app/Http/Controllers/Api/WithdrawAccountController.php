<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawAccountResource;
use App\Models\UserWallet;
use App\Models\WithdrawAccount;
use App\Models\WithdrawMethod;
use App\Services\WithdrawAccountService;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawAccountController extends Controller
{
    use ApiResponseTrait, ImageUpload;

    protected $withdrawAccountService;

    public function __construct(WithdrawAccountService $withdrawAccountService)
    {
        $this->withdrawAccountService = $withdrawAccountService;
    }

    public function config()
    {
        $user = request()->user();

        // Check if withdraw feature is enabled
        if (! setting('user_withdraw', 'permission')) {
            return $this->error(__('Withdraw feature is not enabled!'));
        }

        $wallets = [];

        if (setting('multiple_currency', 'permission')) {
            $wallets = UserWallet::query()
                ->with(['currency'])
                ->where('user_id', $user->id)
                ->get();
        }

        $withdrawMethods = WithdrawMethod::where('status', true)->get();

        return $this->success([
            'wallets' => $wallets,
            'withdraw_methods' => $withdrawMethods,
            'settings' => [
                'kyc_required' => setting('kyc_withdraw', 'permission'),
                'multiple_currency_enabled' => setting('multiple_currency', 'permission'),
            ],
        ], __('Withdraw account configuration'));
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'keyword' => 'nullable|string',
            'method_id' => 'nullable|integer',
            'wallet_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $query = $this->withdrawAccountService->getAccounts($request);

        $accounts = $query->when($request->per_page || $request->page, function ($q) use ($request) {
            return $q->paginate($request->per_page ?? 15);
        }, function ($q) {
            return $q->get();
        });

        if (! $request->per_page && ! $request->page) {
            return $this->success([
                'accounts' => WithdrawAccountResource::collection($accounts),
            ], __('Withdraw accounts'));
        }

        return $this->success([
            'accounts' => WithdrawAccountResource::collection($accounts),
            'pagination' => [
                'current_page' => $accounts?->currentPage(),
                'last_page' => $accounts?->lastPage(),
                'per_page' => $accounts?->perPage(),
                'total' => $accounts?->total(),
            ],
        ], __('Withdraw accounts'));
    }

    public function store(Request $request)
    {
        $validation = $this->withdrawAccountService->validateCreate($request);

        if (isValidationException($validation)) {
            return $this->error($validation->getMessage(), 422, $validation->errors());
        }

        $result = $this->withdrawAccountService->createAccount($request);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(
            new WithdrawAccountResource($result),
            __('Withdraw account created successfully!')
        );
    }

    public function show($id)
    {
        $user = request()->user();

        $account = WithdrawAccount::with(['method', 'wallet.currency'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $account) {
            return $this->error(__('Withdraw account not found'), 404);
        }

        // Process credentials
        $credentials = json_decode($account->credentials ?? '[]', true) ?? [];
        $account->fields = collect($credentials)->map(function ($field) {
            if (isset($field['type']) && $field['type'] === 'file' &&
                isset($field['value']) && $field['value'] &&
                file_exists(base_path('assets/'.$field['value']))) {
                $field['value'] = asset($field['value']);
            }

            return $field;
        })->toArray();

        return $this->success(
            new WithdrawAccountResource($account),
            __('Withdraw account details')
        );
    }

    public function update(Request $request, $id)
    {
        $result = $this->withdrawAccountService->updateAccount($request, $id);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success(
            new WithdrawAccountResource($result),
            __('Withdraw account updated successfully!')
        );
    }

    public function destroy($id)
    {
        $result = $this->withdrawAccountService->deleteAccount($id);

        if (isValidationException($result)) {
            return $this->error($result->getMessage(), 422, $result->errors());
        }

        return $this->success([], __('Withdraw account deleted successfully!'));
    }

    public function getWithdrawMethodFields($fields)
    {
        $formattedFields = [];
        foreach (json_decode($fields, true) ?? [] as $key => $field) {
            if (
                isset($field['type']) && $field['type'] === 'file' &&
                isset($field['value']) && $field['value'] &&
                file_exists(base_path('assets/'.$field['value']))
            ) {
                $field['value'] = asset($field['value']);
            }
            $formattedFields[] = $field;
        }

        return $formattedFields;
    }

    public function getWithdrawMethods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $methods = $this->withdrawAccountService->getWithdrawMethods($request->currency);
        $methods->map(function ($method) {
            $method->fields = $this->getWithdrawMethodFields($method->fields);

            return $method;
        });

        return $this->success($methods, __('Withdraw methods'));
    }

    public function getMethodDetails($id)
    {
        $user = request()->user();
        $method = WithdrawMethod::where('status', true)
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $method) {
            return $this->error(__('Withdraw method not found'), 404);
        }

        return $this->success($method, __('Withdraw method details'));
    }
}
