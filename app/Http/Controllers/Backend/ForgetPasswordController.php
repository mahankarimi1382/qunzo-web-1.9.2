<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    use NotifyTrait;

    public function showForgetPasswordForm()
    {
        return view('backend.auth.forget_password');
    }

    public function submitForgetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:admins',
        ]);

        try {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $shortcodes = [
                '[[token]]' => route('admin.reset.password.now', ['token' => $token]),
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => '#',
            ];

            $this->mailNotify($request->email, 'admin_forget_password', $shortcodes);

            notify()->success(__('We have e-mailed your password reset link!'));

            return back();
        } catch (Exception $exception) {
            Log::error('Forget password error: ' . $exception->getMessage());
            notify()->error(__('Sorry, Something went wrong.'));

            return back();
        }
    }

    public function showResetPasswordForm()
    {

        return view('backend.auth.reset_password');
    }

    public function submitResetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:admins',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        try {

            $updatePassword = DB::table('password_reset_tokens')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token,
                ])
                ->first();

            if (! $updatePassword) {
                return back()->withInput()->with('error', 'Invalid token!');
            }

            Admin::where('email', $request->email)
                ->update(['password' => bcrypt($request->password)]);

            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
            notify()->success(__('Your password has been changed!'));

            return redirect('admin/login');
        } catch (Exception $exception) {
            notify()->error(__('Sorry, Something went wrong.'));

            return back();
        }
    }
}
