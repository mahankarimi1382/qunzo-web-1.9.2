<?php

namespace App\Http\Controllers\Api;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WithdrawAccount;
use App\Models\WithdrawalSchedule;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    use ApiResponseTrait, NotifyTrait, Payment;

    public function __invoke(Request $request)
    {
        $user = request()->user();
        if (! setting('user_withdraw', 'permission') || ! $user->withdraw_status) {
            $this->error(__('Withdraw currently unavailable!'), 422);
        }

        $withdrawOffDays = WithdrawalSchedule::where('status', 0)->pluck('name')->toArray();
        $date = Carbon::now();
        $today = $date->format('l');

        if (in_array($today, $withdrawOffDays)) {
            return $this->error(__('Today is the off day of withdraw'), 422);
        }

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'withdraw_account_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $input = $request->all();
        $amount = (float) $input['amount'];

        $withdrawAccount = WithdrawAccount::find($input['withdraw_account_id']);

        if (! $withdrawAccount) {
            return $this->error(__('Withdraw account not found'), 422);
        }

        $withdrawMethod = $withdrawAccount->method;

        if ($amount < $withdrawMethod->min_withdraw || $amount > $withdrawMethod->max_withdraw) {
            $currencySymbol = setting('currency_symbol', 'global');

            $message = __('Please withdraw the Amount within the range :min to :max', [
                'min' => $currencySymbol . $withdrawMethod->min_withdraw,
                'max' => $currencySymbol . $withdrawMethod->max_withdraw,
            ]);

            return $this->error($message, 422);
        }

        $charge = $withdrawMethod->charge_type == 'percentage' ? (($withdrawMethod->charge / 100) * $amount) : $withdrawMethod->charge;
        $totalAmount = $amount + (float) $charge;

        if ($user->balance < $totalAmount) {
            return $this->error(__('Insufficient Balance'), 422);
        }

        $user->decrement('balance', $totalAmount);

        $payAmount = $amount * $withdrawMethod->rate;

        $type = $withdrawMethod->type == 'auto' ? TxnType::WithdrawAuto : TxnType::Withdraw;

        $txnInfo = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'charge' => $charge,
            'final_amount' => $totalAmount,
            'wallet_type' => $withdrawAccount->wallet?->id ?? 'default',
            'description' => 'Withdraw With ' . $withdrawAccount->method_name,
            'type' => $type,
            'status' => TxnStatus::Pending,
            'pay_amount' => $payAmount,
            'pay_currency' => $withdrawMethod->currency,
            'method' => $withdrawMethod->name ?? 'User',
            'manual_field_data' => json_decode($withdrawAccount->credentials, true),
        ]);

        if ($withdrawMethod->type == 'auto') {
            $gatewayCode = $withdrawMethod->gateway->gateway_code;

            self::withdrawAutoGateway($gatewayCode, $txnInfo, true);
        }

        $shortcodes = [
            '[[full_name]]' => $txnInfo->user->full_name,
            '[[txn]]' => $txnInfo->tnx,
            '[[method_name]]' => $withdrawMethod->name,
            '[[withdraw_amount]]' => $txnInfo->amount . $withdrawMethod->currency,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
        ];

        $this->mailNotify(setting('site_email', 'global'), 'withdraw_request', $shortcodes);
        $this->pushNotify('withdraw_request', $shortcodes, route('admin.withdraw.pending'), $user->id);
        $this->smsNotify('withdraw_request', $shortcodes, $user->phone);

        return $this->success([
            'transaction' => $txnInfo,
        ], __('Withdraw request successful'));
    }
}
