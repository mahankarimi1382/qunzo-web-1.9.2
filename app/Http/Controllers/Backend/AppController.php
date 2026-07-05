<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Subscriber;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller implements HasMiddleware
{
    use ImageUpload;
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:subscriber-list|subscriber-mail-send', ['only' => ['subscribers']]),
            new Middleware('permission:subscriber-mail-send', ['only' => ['mailSendSubscriber', 'mailSendSubscriberNow']]),
        ];
    }

    public function subscribers(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $order = $request->order ?? 'asc';
        $search = $request->search ?? null;
        $subscribes = Subscriber::order($order)->search($search)->paginate($perPage);

        return view('backend.subscriber.index', ['subscribes' => $subscribes]);
    }

    public function mailSendSubscriber()
    {
        return view('backend.subscriber.mail_send');
    }

    public function mailSendSubscriberNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $input = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $shortcodes = [
                '[[subject]]' => $input['subject'],
                '[[message]]' => $input['message'],
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => '#',
            ];

            $subscribers = Subscriber::all();
            foreach ($subscribers as $subscriber) {
                $this->mailNotify($subscriber->email, 'subscriber_mail', $shortcodes);
            }

            $status = 'success';
            $message = __('Mail Send Successfully');
        } catch (Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function profile()
    {
        return view('backend.profile.profile');
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $user = Admin::findOrFail(Auth::user()->id);
            $user->update([
                'avatar' => $request->hasFile('avatar') ? self::imageUploadTrait($request->avatar, $user->avatar) : $user->avatar,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $status = 'success';
            $message = __('Profile Update Successfully');
        } catch (Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function passwordChange()
    {
        return view('backend.profile.password_change');
    }

    public function passwordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back();
        }

        auth()->user()->update(['password' => Hash::make($request->new_password)]);
        notify()->success('Password Changed Successfully');

        return redirect()->back();
    }

    public function securitySettings()
    {
        $admin = auth('admin')->user();
        $twoFaStatus = $admin->two_fa;

        return view('backend.profile.security_settings', compact('twoFaStatus', 'admin'));
    }

    public function securitySettingsUpdate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:generate,disable,enable',
        ]);

        if ($request->type == 'generate') {
            $this->generate2fa();
            notify()->success('2FA Secret Key Generated Successfully');

            return redirect()->back();
        }

        if ($request->type == 'disable') {
            $this->disable2fa();
            notify()->success('2FA Disabled Successfully');
        }

        if ($request->type == 'enable') {
            $this->enable2fa($request->otp);
            notify()->success('2FA Enabled Successfully');
        }

        return redirect()->back();
    }

    private function generate2fa()
    {
        $user = auth('admin')->user();

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        $user->two_fa_secret = $secret;
        $user->two_fa = 0;
        $user->save();
    }

    private function disable2fa()
    {
        $user = auth('admin')->user();
        $user->two_fa_secret = null;
        $user->two_fa = 0;
        $user->save();
    }

    private function enable2fa($code)
    {
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey(auth('admin')->user()->two_fa_secret, $code);

        if (! $valid) {
            notify()->error(__('Invalid OTP'));

            return redirect()->back();
        }

        $user = auth('admin')->user();
        $user->two_fa = 1;
        $user->save();
    }

    public function applicationInfo()
    {
        $mySqlVersion = mySqlVersion();
        $required_extensions = ['bcmath', 'ctype', 'json', 'mbstring', 'zip', 'zlib', 'openssl', 'pcre', 'filter', 'hash', 'session', 'tokenizer', 'xml', 'dom',  'curl', 'fileinfo', 'gd', 'pdo_mysql'];

        $success = '<i data-lucide="check-circle" class="text-success"></i>';
        $error = '<i data-lucide="circle-x" class="text-danger"></i>';

        return view('backend.system.index', ['success' => $success, 'error' => $error, 'required_extensions' => $required_extensions, 'mySqlVersion' => $mySqlVersion]);
    }

    public function clearCache()
    {
        Artisan::call('optimize:clear');

        notify()->success(__('Cache cleared successfully!'));

        return back();
    }
}
