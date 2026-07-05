<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:currency-manage', ['only' => ['index']]),
            new Middleware('permission:currency-create', ['only' => ['create', 'store']]),
            new Middleware('permission:currency-edit', ['only' => ['edit', 'update']]),
            new Middleware('permission:currency-delete', ['only' => ['delete']]),
        ];
    }

    public function index()
    {
        $currencies = Currency::latest()->paginate(10);

        return view('backend.currency.index', ['currencies' => $currencies]);
    }

    public function create()
    {
        return view('backend.currency.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required|uppercase|unique:currencies,code',
            'symbol' => 'required|unique:currencies,symbol',
            'conversion_rate' => 'required|numeric',
            'icon' => 'nullable|mimes:png,jpg,jpeg,gif,webp',
            'status' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            DB::beginTransaction();

            $currency = [
                'name' => $request->name,
                'code' => $request->code,
                'symbol' => $request->symbol,
                'conversion_rate' => $request->conversion_rate,
                'type' => $request->type,
                'status' => $request->status,
            ];

            if ($request->hasFile('icon')) {
                $currency['icon'] = self::imageUploadTrait($request->icon, folderPath: 'currency');
            }

            Currency::create($currency);

            DB::commit();

            notify()->success(__('Currency created successfully!'));

            return redirect()->route('admin.currency.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again.'));

            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $currencyInfo = Currency::findOrFail($id);

        return view('backend.currency.edit', ['currencyInfo' => $currencyInfo]);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required|uppercase|unique:currencies,code,'.$id,
            'symbol' => 'required|unique:currencies,symbol,'.$id,
            'conversion_rate' => 'required|numeric',
            'icon' => 'nullable|mimes:png,jpg,jpeg,gif,webp',
            'status' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            DB::beginTransaction();

            $currency = Currency::findOrFail($id);
            $currency->name = $request->name;
            $currency->code = $request->code;
            $currency->type = $request->type;
            $currency->symbol = $request->symbol;
            $currency->conversion_rate = $request->conversion_rate;
            $currency->status = $request->status;
            if ($request->hasFile('icon')) {

                if (file_exists($currency->icon)) {
                    self::fileDelete($currency->icon);
                }

                $image_url = self::imageUploadTrait($request->icon, folderPath: 'currency');
                $currency->icon = $image_url;
            }

            $currency->save();

            DB::commit();

            notify()->success(__('Currency updated successfully!'));

            return redirect()->route('admin.currency.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong.'));

            return redirect()->back();
        }
    }

    public function delete($id)
    {
        $currency = Currency::with('wallets')->findOrFail($id);

        if ($currency->wallets()->count() > 0) {
            notify()->error(__('This currency is linked to active wallets and cannot be deleted. Please remove the associated wallets first.'));

            return redirect()->back();
        }

        try {

            DB::beginTransaction();

            if ($currency->icon !== null) {
                self::fileDelete($currency->icon);
            }

            $currency->delete();

            DB::commit();

            notify()->success(__('Currency deleted successfully!'));

            return redirect()->back();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
            notify()->error(__('Sorry! Something went wrong. Please try again.'));

            return redirect()->back();
        }
    }
}
