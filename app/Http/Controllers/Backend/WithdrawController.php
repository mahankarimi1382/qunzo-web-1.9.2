<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Gateway;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WithdrawalSchedule;
use App\Models\WithdrawMethod;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller implements HasMiddleware
{
    use ImageUpload;
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:withdraw-method-manage', ['only' => ['methods', 'methodCreate', 'methodStore', 'methodEdit', 'methodUpdate']]),
            new Middleware('permission:withdraw-list|withdraw-action', ['only' => ['pending', 'history']]),
            new Middleware('permission:withdraw-action', ['only' => ['withdrawAction', 'actionNow']]),
            new Middleware('permission:withdraw-schedule', ['only' => ['schedule', 'scheduleUpdate']]),
        ];
    }

    public function methods($type)
    {
        $button = [
            'name' => __('ADD NEW'),
            'icon' => 'plus',
            'route' => route('admin.withdraw.method.create', $type),
        ];
        $withdrawMethods = WithdrawMethod::whereType($type)->get();

        return view('backend.withdraw.method', ['withdrawMethods' => $withdrawMethods, 'button' => $button, 'type' => $type]);
    }

    public function methodCreate($type)
    {
        $button = [
            'name' => __('Back'),
            'icon' => 'corner-down-left',
            'route' => route('admin.withdraw.method.list', $type),
        ];
        $gateways = Gateway::where('status', true)->whereNot('is_withdraw', '=', '0')->get();
        $currencies = Currency::where('status', true)->get();

        return view('backend.withdraw.method_create', ['button' => $button, 'currencies' => $currencies, 'type' => $type, 'gateways' => $gateways]);
    }

    public function methodStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'required_if:type,==,manual',
            'gateway_id' => 'required_if:type,==,auto',
            'name' => 'required',
            'currency' => 'required',
            'required_time' => 'required_if:type,==,manual',
            'required_time_format' => 'required_if:type,==,manual',
            'charge' => 'required',
            'charge_type' => 'required',
            'min_withdraw' => 'required',
            'max_withdraw' => 'required',
            'status' => 'required',
            'fields' => 'required_if:type,==,manual',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $fields = null;
            if ($request->type == 'auto') {

                $withdrawGateways = Gateway::find($request->gateway_id);
                $withdrawFields = explode(',', $withdrawGateways->is_withdraw);

                $fields = array_map(function ($field) {
                    return [
                        'name' => $field,
                        'type' => 'text',
                        'validation' => 'required',
                    ];
                }, $withdrawFields);
            }

            WithdrawMethod::create([
                'icon' => $request->icon !== null ? self::imageUploadTrait($request->icon, folderPath: 'withdraw_methods') : null,
                'gateway_id' => $request->gateway_id ?? null,
                'type' => $request->type,
                'name' => $request->name,
                'required_time' => $request->required_time ?? 0,
                'required_time_format' => $request->required_time_format ?? 'hour',
                'currency' => $request->currency,
                'charge' => $request->charge,
                'charge_type' => $request->charge_type,
                'min_withdraw' => $request->min_withdraw,
                'max_withdraw' => $request->max_withdraw,
                'status' => $request->status,
                'fields' => json_encode($fields ?? $request->fields),
            ]);

            notify()->success(__('Withdraw method created successfully'));

            return redirect()->route('admin.withdraw.method.list', $request->type);
        } catch (\Exception $exception) {
            notify()->warning(__('Sorry, something is wrong!'));

            return back();
        }
    }

    public function methodEdit($type)
    {
        $button = [
            'name' => __('Back'),
            'icon' => 'corner-down-left',
            'route' => route('admin.withdraw.method.list', $type),
        ];

        $withdrawMethod = WithdrawMethod::find(\request('id'));
        $supported_currencies = Gateway::find($withdrawMethod->gateway_id)->supported_currencies ?? [];
        $currencies = Currency::where('status', true)->get();

        return view('backend.withdraw.method_edit', ['button' => $button, 'currencies' => $currencies, 'withdrawMethod' => $withdrawMethod, 'type' => $type, 'supported_currencies' => $supported_currencies]);
    }

    public function methodUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'currency' => 'required',
            'required_time' => 'required_if:type,==,manual',
            'required_time_format' => 'required_if:type,==,manual',
            'charge' => 'required',
            'charge_type' => 'required',
            'min_withdraw' => 'required',
            'max_withdraw' => 'required',
            'status' => 'required',
            'fields' => 'required_if:type,==,manual',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $withdrawMethod = WithdrawMethod::find($id);

            $data = [
                'name' => $request->name,
                'required_time' => $request->required_time ?? $withdrawMethod->required_time,
                'required_time_format' => $request->required_time_format ?? $withdrawMethod->required_time_format,
                'currency' => $request->currency ?? $withdrawMethod->currency,
                'charge' => $request->charge,
                'charge_type' => $request->charge_type,
                'min_withdraw' => $request->min_withdraw,
                'max_withdraw' => $request->max_withdraw,
                'status' => $request->status,
                'fields' => $request->fields !== null ? json_encode($request->fields) : $withdrawMethod->fields,
            ];

            if ($request->hasFile('icon')) {
                $icon = self::imageUploadTrait($request->icon, $withdrawMethod->icon, 'withdraw_methods');
                $data = array_merge($data, ['icon' => $icon]);
            }

            $withdrawMethod->update($data);

            notify()->success(__('Withdraw method updated successfully'));

            return redirect()->route('admin.withdraw.method.list', $withdrawMethod->type);
        } catch (\Exception $exception) {
            notify()->warning(__('Sorry, something is wrong!'));

            return back();
        }
    }

    public function pending(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $withdrawals = Transaction::query()
            ->with('user', 'userWallet')
            ->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])
            ->where('status', TxnStatus::Pending)
            ->latest()
            ->search($search)
            ->paginate($perPage);

        return view('backend.withdraw.pending', ['withdrawals' => $withdrawals]);
    }

    public function history(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $status = $request->status ?? 'all';
        $search = $request->search ?? null;
        $withdrawals = Transaction::with('user')
            ->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])
            ->search($search)
            ->when(in_array(request('sort_field'), ['created_at', 'amount', 'charge', 'method', 'status', 'tnx']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->status($status)
            ->paginate($perPage);

        return view('backend.withdraw.history', ['withdrawals' => $withdrawals]);
    }

    public function withdrawAction($id)
    {

        $data = Transaction::find($id);

        return view('backend.withdraw.include.__withdraw_action', ['data' => $data, 'id' => $id])->render();
    }

    public function actionNow(Request $request)
    {
        try {
            $id = $request->id;
            $approvalCause = $request->message;
            $transaction = Transaction::find($id);
            $user = User::find($transaction->user_id);

            if ($request->has('approve')) {

                if (setting('withdraw', 'referral_level')) {
                    $level = LevelReferral::where('type', 'withdraw')->max('the_order') + 1;
                    creditCurrencyWiseReferralBonus($transaction->user, 'withdraw', $transaction->amount, $level, 1, null, $transaction->userWallet);
                }

                (new Txn)->update($transaction->tnx, TxnStatus::Success, $transaction->user_id, $approvalCause);
                $shortcodes = [
                    '[[full_name]]' => $transaction->user->full_name,
                    '[[amount]]' => formatAmount($transaction->amount, $transaction->currency),
                    '[[charge]]' => formatAmount($transaction->charge, $transaction->currency),
                    '[[wallet]]' => data_get($transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                    '[[gateway]]' => $transaction->method,
                    '[[request_at]]' => $transaction->created_at,
                    '[[total_amount]]' => formatAmount($transaction->final_amount, $transaction->currency),
                    '[[transaction_link]]' => '',
                    '[[site_title]]' => setting('site_title', 'global'),
                    '[[currency]]' => data_get($transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                ];

                $this->sendNotify($transaction->user->email, 'withdraw_approved', 'User', $shortcodes, $transaction->user->phone, $transaction->user->id, '');

                notify()->success(__('Withdraw approved successfully!'));
            } else {
                (new Txn)->update($transaction->tnx, TxnStatus::Failed, $transaction->user_id, $approvalCause);
                $newTransaction = $transaction->replicate();
                $newTransaction->type = TxnType::Refund;
                $newTransaction->amount = $transaction->amount + $transaction->charge;
                $newTransaction->charge = 0;
                $newTransaction->final_amount = $transaction->amount + $transaction->charge;
                $newTransaction->status = TxnStatus::Success;
                $newTransaction['method'] = 'system';
                $newTransaction->save();

                if ($transaction->wallet_type == null || $transaction->wallet_type == 'default') {
                    $user->increment('balance', $transaction->final_amount);
                } else {
                    $user_wallet = UserWallet::find($transaction->wallet_type);

                    if ($user_wallet) {
                        $user_wallet->increment('balance', $transaction->final_amount);
                    }
                }

                $shortcodes = [
                    '[[full_name]]' => $transaction->user->full_name,
                    '[[amount]]' => formatAmount($transaction->amount, $transaction->currency),
                    '[[charge]]' => formatAmount($transaction->charge, $transaction->currency),
                    '[[wallet]]' => data_get($transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                    '[[gateway]]' => $transaction->method,
                    '[[request_at]]' => $transaction->created_at,
                    '[[total_amount]]' => formatAmount($transaction->final_amount, $transaction->currency),
                    '[[transaction_link]]' => '',
                    '[[rejection_reason]]' => $approvalCause,
                    '[[site_title]]' => setting('site_title', 'global'),
                    '[[currency]]' => data_get($transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                ];

                $this->sendNotify($transaction->user->email, 'withdraw_rejected', 'User', $shortcodes, $transaction->user->phone, $transaction->user->id, '');

                notify()->success(__('Withdraw rejected successfully!'));
            }

            $shortcodes = [
                '[[full_name]]' => $user->full_name,
                '[[txn]]' => $transaction->tnx,
                '[[method_name]]' => $transaction->method,
                '[[withdraw_amount]]' => formatAmount($transaction->amount, $transaction->currency, true),
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => '#',
                '[[message]]' => $transaction->approval_cause,
                '[[status]]' => property_exists($request, 'approve') && $request->approve !== null ? 'Approved' : 'Rejected',
            ];

            $this->mailNotify($user->email, 'withdraw_request_user', $shortcodes);
            $this->pushNotify('withdraw_request_user', $shortcodes, '/', $user->id);
            $this->smsNotify('withdraw_request_user', $shortcodes, $user->phone);

            return redirect()->back();
        } catch (\Exception $exception) {
            notify()->error(__('Sorry! Something went wrong.'));

            return back();
        }
    }

    public function schedule()
    {
        $schedules = WithdrawalSchedule::all();

        return view('backend.withdraw.schedule', ['schedules' => $schedules]);
    }

    public function scheduleUpdate(Request $request)
    {
        try {
            $updateSchedules = $request->except('_token');
            foreach ($updateSchedules as $name => $status) {
                WithdrawalSchedule::where('name', $name)->update([
                    'status' => $status,
                ]);
            }

            $status = 'success';
            $message = __('Withdrawal Schedule Update successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }
}
