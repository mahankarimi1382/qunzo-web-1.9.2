<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('backend.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->guard()->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(setting('site_admin_prefix', 'global'));
        }

        notify()->warning(__('The provided credentials do not match our records.'));

        return back();
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->regenerateToken();
        session()->forget('admin_two_fa_verified');

        return redirect()->route('admin.login');
    }
}
