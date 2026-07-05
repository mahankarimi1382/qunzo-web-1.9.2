<?php

namespace App\Http\Controllers\Backend;

use Addons\P2PTrading\Models\AdsPaymentMethod;
use Addons\P2PTrading\Models\PaymentMethod;
use Addons\P2PTrading\Models\UserPaymentMethod;
use App\Enums\CurrencyType;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class P2pPaymentMethodController extends Controller implements HasMiddleware
{
    public function __construct()
    {
        abort_if(! addonActive('p2p-trading'), 404);
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:p2p-payment-method-manage'),
        ];
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::with('currency')->latest()->paginate(10);

        return view('backend.p2p.payment-method.index', ['paymentMethods' => $paymentMethods]);
    }

    public function create()
    {
        $currencies = Currency::where('type', CurrencyType::Fiat)->get();

        return view('backend.p2p.payment-method.create', ['currencies' => $currencies]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:currencies,id',
            'name' => 'required',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required_with:fields',
            'fields.*.type' => 'required_with:fields|in:text,textarea,file',
            'fields.*.validation' => 'required_with:fields|in:required,nullable',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Ensure currency is fiat
        $currency = Currency::findOrFail($request->currency_id);
        if ($currency->type !== CurrencyType::Fiat) {
            notify()->error(__('Only fiat currencies are allowed for payment methods.'));

            return redirect()->back()->withInput();
        }

        try {
            DB::beginTransaction();

            $fields = $request->fields ? array_values(array_filter($request->fields, fn($f) => ! empty($f['name']))) : [];

            PaymentMethod::create([
                'currency_id' => $request->currency_id,
                'name' => $request->name,
                'fields' => $fields,
            ]);

            DB::commit();

            notify()->success(__('Payment method created successfully!'));

            return redirect()->route('admin.p2p.payment-method.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again.'));

            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::with('currency')->findOrFail($id);
        $currencies = Currency::where('type', CurrencyType::Fiat)->get();

        return view('backend.p2p.payment-method.edit', [
            'paymentMethod' => $paymentMethod,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:currencies,id',
            'name' => 'required',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required_with:fields',
            'fields.*.type' => 'required_with:fields|in:text,textarea,file',
            'fields.*.validation' => 'required_with:fields|in:required,nullable',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $currency = Currency::findOrFail($request->currency_id);
        if ($currency->type !== CurrencyType::Fiat) {
            notify()->error(__('Only fiat currencies are allowed for payment methods.'));

            return redirect()->back()->withInput();
        }

        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::findOrFail($id);

            $fields = $request->fields ? array_values(array_filter($request->fields, fn($f) => ! empty($f['name']))) : [];

            $paymentMethod->update([
                'currency_id' => $request->currency_id,
                'name' => $request->name,
                'fields' => $fields,
            ]);

            DB::commit();

            notify()->success(__('Payment method updated successfully!'));

            return redirect()->route('admin.p2p.payment-method.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again.'));

            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $adsCount = AdsPaymentMethod::where('payment_method_id', $id)->count();
        $userCount = UserPaymentMethod::where('payment_method_id', $id)->count();

        if ($adsCount > 0 || $userCount > 0) {
            notify()->error(__('This payment method is linked to ads or user payment methods and cannot be deleted.'));

            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $paymentMethod->delete();

            DB::commit();

            notify()->success(__('Payment method deleted successfully!'));

            return redirect()->back();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again.'));

            return redirect()->back();
        }
    }
}
