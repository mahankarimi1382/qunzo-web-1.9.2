<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ImageUpload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:site-setting|email-setting', ['only' => ['update']]),
            new Middleware('permission:site-setting', ['only' => ['siteSetting', 'seoMeta']]),
            new Middleware('permission:email-setting', ['only' => ['mailSetting']]),
        ];
    }

    public static function siteSetting()
    {
        return view('backend.setting.site_setting.index');
    }

    public function transactionSettings()
    {
        return view('backend.setting.transactions');
    }

    public static function mailSetting()
    {
        return view('backend.setting.mail');
    }

    public static function mailConnectionTest(Request $request)
    {

        try {
            Mail::raw('Testing SMTP connection successful', function ($message) use ($request) {
                $message->to($request->email);
            });

            notify()->success(__('SMTP connection test successful.'));

            return back();
        } catch (\Exception $exception) {
            notify()->error(__('SMTP connection test failed:').' '.$exception->getMessage());

            return back();
        }
    }

    public function update(Request $request)
    {
        try {
            if ($request->ajax()) {

                $path = Setting::get($request->get('name'));

                if (file_exists(public_path($path))) {
                    @unlink($path);
                }

                return response()->json([
                    'success' => true,
                ]);
            }

            DB::beginTransaction();

            $section = $request->section;
            $rules = Setting::getValidationRules($section);
            $data = $request->validate($rules);
            $validSettings = array_keys($rules);
            foreach ($data as $key => $val) {

                if (in_array($key, $validSettings)) {
                    if ($request->hasFile($key)) {
                        $oldImage = Setting::get($key, $section);
                        $val = self::imageUploadTrait($val, $oldImage, 'settings');
                    }

                    Setting::add($key, $val, Setting::getDataType($key, $section));
                }
            }

            DB::commit();
            notify()->success(__('Settings has been saved'));

            return redirect()->back();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function updateReferralRules(Request $request)
    {
        // Create validation rules
        $rules = [
            'referral_rules.*.name' => 'required|string',
            'referral_rules.*.amount' => 'required|numeric',
        ];

        // Validate the request data
        $request->validate($rules);

        try {
            DB::beginTransaction();

            if ($request->has('referral_rules')) {
                Setting::updateOrCreate([
                    'name' => 'referral_rules',
                ], [
                    'val' => json_encode(array_values($request->get('referral_rules'))),
                ]);
            }
            notify()->success(__('Referral rules has been saved'));
            DB::commit();

            return redirect()->back();
        } catch (Exception $exception) {
            DB::rollBack();
            notify()->error(__('Sorry, something went wrong.'));

            return redirect()->back();
        }
    }

    public function seoMeta()
    {
        return view('backend.setting.seo-meta');
    }
}
