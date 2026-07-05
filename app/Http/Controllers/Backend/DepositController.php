<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\DepositMethod;
use App\Models\Gateway;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class DepositController extends Controller implements HasMiddleware
{
    use ImageUpload;
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:automatic-gateway-manage|manual-gateway-manage', ['only' => ['methodList', 'createMethod', 'methodStore', 'methodEdit', 'methodUpdate']]),
            new Middleware('permission:deposit-list|deposit-action', ['only' => ['pending', 'history']]),
            new Middleware('permission:deposit-action', ['only' => ['depositAction', 'actionNow']]),
        ];
    }

    public function methodList($type)
    {
        $button = [
            'name' => __('ADD NEW'),
            'icon' => 'plus',
            'route' => route('admin.deposit.method.create', $type),
        ];

        $depositMethods = DepositMethod::where('type', $type)->get();

        return view('backend.deposit.method_list', ['depositMethods' => $depositMethods, 'button' => $button, 'type' => $type]);
    }

    public function createMethod($type)
    {
        $gateways = Gateway::where('status', true)->get();
        $currencies = Currency::where('status', true)->get();

        return view('backend.deposit.create_method', ['type' => $type, 'gateways' => $gateways, 'currencies' => $currencies]);
    }

    public function methodStore(Request $request)
    {
        $currencies = array_merge(Currency::pluck('code')->toArray(), [setting('site_currency', 'global')]);

        $validator = Validator::make($request->all(), [
            'logo' => 'required_if:type,==,manual',
            'name' => 'required',
            'gateway_id' => 'required_if:type,==,auto',
            'method_code' => 'required_if:type,==,manual',
            'currency' => ['required', Rule::in($currencies), 'uppercase'],
            'currency_symbol' => 'required',
            'status' => 'required',
            'minimum_deposit' => 'required',
            'maximum_deposit' => 'required',
            'field_options' => 'required_if:type,==,manual',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            if ($request->gateway_id !== null) {
                $gateway = Gateway::find($request->gateway_id);
                $methodCode = $gateway->gateway_code.'-'.strtolower($request->currency);
            }

            $data = [
                'logo' => $request->hasFile('logo') ? self::imageUploadTrait($request->logo, folderPath: 'deposit_methods') : null,
                'name' => $request->name,
                'type' => $request->type,
                'gateway_id' => $request->gateway_id ?? null,
                'gateway_code' => $request->method_code ?? $methodCode,
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'charge' => $request->charge ? $request->charge : 0,
                'charge_type' => $request->charge_type ? $request->charge_type : 'percentage',
                'minimum_deposit' => $request->minimum_deposit,
                'maximum_deposit' => $request->maximum_deposit,
                'status' => $request->status,
                'field_options' => $request->field_options,
                'payment_details' => $request->payment_details !== null ? Purifier::clean(htmlspecialchars_decode($request->payment_details)) : null,
            ];

            $depositMethod = DepositMethod::create($data);

            notify()->success(__('Deposit method added successfully!'));

            return redirect()->route('admin.deposit.method.list', $depositMethod->type);
        } catch (\Exception $exception) {

            notify()->error($exception->getMessage());

            return redirect()->back();
        }
    }

    public function methodEdit($type)
    {
        $gateways = Gateway::where('status', true)->get();
        $method = DepositMethod::find(\request('id'));
        $supported_currencies = Gateway::find($method->gateway_id)->supported_currencies ?? [];
        $currencies = Currency::where('status', true)->get();

        return view('backend.deposit.edit_method', ['method' => $method, 'type' => $type, 'gateways' => $gateways, 'supported_currencies' => $supported_currencies, 'currencies' => $currencies]);
    }

    public function methodUpdate($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'gateway_id' => 'required_if:type,==,auto',
            'currency' => 'required',
            'currency_symbol' => 'required',
            'charge' => 'required',
            'charge_type' => 'required',
            'status' => 'required',
            'minimum_deposit' => 'required',
            'maximum_deposit' => 'required',
            'field_options' => 'required_if:type,==,manual',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $depositMethod = DepositMethod::find($id);

            $user = User::find(Auth::user()->id);

            $data = [
                'name' => $request->name,
                'type' => $request->type,
                'gateway_id' => $request->gateway_id ?? null,
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'charge' => $request->charge ? $request->charge : 0,
                'charge_type' => $request->charge_type ? $request->charge_type : 'percentage',
                'minimum_deposit' => $request->minimum_deposit,
                'maximum_deposit' => $request->maximum_deposit,
                'status' => $request->status,
                'field_options' => $request->field_options,
                'payment_details' => $request->payment_details !== null ? Purifier::clean(htmlspecialchars_decode($request->payment_details)) : null,
            ];

            if ($request->hasFile('logo')) {
                $logo = self::imageUploadTrait($request->logo, $depositMethod->logo, 'deposit_methods');
                $data = array_merge($data, ['logo' => $logo]);
            }

            $depositMethod->update($data);

            notify()->success(__('Deposit method updated successfully!'));

            return redirect()->route('admin.deposit.method.list', $depositMethod->type);
        } catch (\Exception $exception) {

            notify()->error($exception->getMessage());

            return redirect()->back();
        }

        return null;
    }

    public function pending(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $deposits = Transaction::query()
            ->with([
                'user',
                'userWallet.currency',
            ])
            ->where('status', TxnStatus::Pending)
            ->where('type', TxnType::ManualDeposit)
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
            ->latest()
            ->paginate($perPage);

        return view('backend.deposit.manual', ['deposits' => $deposits]);
    }

    public function history(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $status = $request->status ?? 'all';
        $deposits = Transaction::query()
            ->with([
                'user',
                'userWallet.currency',
            ])
            ->whereIn('type', [
                TxnType::Deposit,
                TxnType::ManualDeposit,
            ])
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
            ->latest()
            ->paginate($perPage);

        return view('backend.deposit.history', ['deposits' => $deposits]);
    }

    public function depositAction($id)
    {
        $data = Transaction::find($id);

        return view('backend.deposit.include.__deposit_action', ['data' => $data, 'id' => $id])->render();
    }

    public function actionNow(Request $request)
    {
        try {

            DB::beginTransaction();
            $id = $request->id;
            $approvalCause = $request->message;
            $transaction = Transaction::find($id);

            if ($request->has('approve')) {

                if (setting('deposit', 'referral_level')) {
                    $level = LevelReferral::where('type', 'deposit')->max('the_order') + 1;
                    creditCurrencyWiseReferralBonus($transaction->user, 'deposit', $transaction->amount, $level, 1, null, $transaction->userWallet);
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

                $this->sendNotify($transaction->user->email, 'user_manual_deposit_approved', 'User', $shortcodes, $transaction->user->phone, $transaction->user->id, '');

                notify()->success(__('Approved successfully'));
            } elseif ($request->has('reject')) {
                (new Txn)->update($transaction->tnx, TxnStatus::Failed, $approvalCause);

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

                $this->sendNotify($transaction->user->email, 'user_manual_deposit_rejected', 'User', $shortcodes, $transaction->user->phone, $transaction->user->id, '');

                notify()->success(__('Rejected successfully'));
            }

            DB::commit();

            return redirect()->back();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            notify()->error(__('Sorry! Something went wrong.'));

            return redirect()->back();
        }
    }
}
