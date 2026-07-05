<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\User;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    use NotifyTrait;

    public function create(Request $request)
    {
        $page = Page::currentTheme()->where('code', 'resetpassword')->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::currentTheme()->where('code', 'resetpassword')->where('locale', defaultLocale())->first();
        }

        $data = json_decode($page->data, true);

        return view('frontend::auth.reset-password', ['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token,
            ])
            ->first();

        if (! $updatePassword) {
            notify()->error(__('Invalid token!'));

            return redirect()->route('password.request');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        notify()->success(__('Your password has been changed!'));

        return redirect()->route('login');
    }
}
