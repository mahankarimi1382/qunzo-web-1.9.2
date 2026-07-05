<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\DepositMethod;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddMoneyService
{
    use ImageUpload, NotifyTrait, Payment;

    public function processAddMoney(Request $request)
    {
        $user = request()->user();
        try {
            $gatewayInfo = DepositMethod::findOrFail($request->payment_gateway);
            $amount = $request->amount;
            $wallet = UserWallet::find($request->user_wallet);
            $charge = $gatewayInfo->charge_type == 'percentage' ? ($gatewayInfo->charge / 100) * $amount : $gatewayInfo->charge;
            $finalAmount = $amount + $charge;
            $payAmount = $finalAmount;
            $depositType = TxnType::Deposit;

            if ($request->manual_data !== null && $gatewayInfo->type == 'manual') {
                $depositType = TxnType::ManualDeposit;
                $manualData = $request->manual_data;

                foreach ($manualData as $key => $value) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $manualData[$key] = self::imageUploadTrait($value);
                    }
                }

                $shortcodes = [
                    '[[amount]]' => $request->amount,
                    '[[charge]]' => $charge,
                    '[[wallet]]' => data_get($wallet, 'currency.name', 'Default'),
                    '[[currency]]' => data_get($wallet, 'currency.code', setting('site_currency', 'global')),
                    '[[gateway]]' => $gatewayInfo->name,
                    '[[request_at]]' => date('d M, Y h:i A'),
                    '[[total_amount]]' => $finalAmount,
                    '[[request_link]]' => route('admin.deposit.manual.pending'),
                    '[[site_title]]' => setting('site_title', 'global'),
                ];

                $this->sendNotify(setting('site_email', 'global'), 'admin_manual_deposit', 'Admin', $shortcodes, $user->phone, $user->id, route('admin.deposit.manual.pending'));
            }

            DB::beginTransaction();

            $userWallet = data_get($wallet, 'id', 'default');

            $txnInfo = Transaction::create([
                'description' => 'Deposit With '.$gatewayInfo->name,
                'amount' => $request->amount,
                'charge' => $charge,
                'final_amount' => $finalAmount,
                'wallet_type' => $userWallet,
                'type' => $depositType,
                'method' => $gatewayInfo->name,
                'callback_url' => $request->get('callback_url'),
                'status' => TxnStatus::Pending,
                'pay_currency' => $gatewayInfo->currency,
                'pay_amount' => $payAmount,
                'user_id' => $user->id,
                'manual_field_data' => $manualData ?? [],
            ]);

            $response = self::depositAutoGateway($gatewayInfo->gateway_code, $txnInfo);
            DB::commit();

            if ($request->expectsJson()) {
                return $response instanceof RedirectResponse ? $response->getTargetUrl() : $txnInfo;
            } else {
                return $response;
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Add money error: '.$th->getMessage());

            return false;
        }
    }

    public function validate(Request $request)
    {
        $user = request()->user();

        if (! setting('user_deposit', 'permission') || ! $user->deposit_status) {
            return makeValidationException([
                'user_deposit' => [__('Add Money currently unavailable!')],
            ]);
        } elseif (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc_deposit' => [__('Please verify your KYC.')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required',
            'amount' => 'required',
            'user_wallet' => setting('multiple_currency') ? 'required' : 'nullable',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        $gatewayInfo = DepositMethod::find($request->payment_gateway);

        if (! $gatewayInfo) {
            return makeValidationException([
                'payment_gateway' => [__('Gateway does not exist!')],
            ]);
        }
        if ($gatewayInfo->type == 'manual') {
            foreach ($gatewayInfo->field_options as $key => $field) {
                if ($field['validation'] == 'required' && ! $request->has('manual_data.'.$field['name'])) {
                    return makeValidationException([
                        'manual_data.'.$field['name'] => [__('The :field is required.', ['field' => $field['name']])],
                    ]);
                }
            }
        }

        $amount = $request->amount;
        $wallet = UserWallet::find($request->user_wallet);

        if ($amount < $gatewayInfo->minimum_deposit || $amount > $gatewayInfo->maximum_deposit) {
            $currencySymbol = setting('currency_symbol', 'global');
            $message = __('Please Deposit the Amount within the range :symbol:min to :symbol:max', [
                'symbol' => data_get($wallet, 'currency.symbol', $currencySymbol),
                'min' => $gatewayInfo->minimum_deposit,
                'max' => $gatewayInfo->maximum_deposit,
            ]);

            return makeValidationException([
                'amount' => [$message],
            ]);
        }

        return true;
    }
}
