<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginActivities;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        $page = Page::currentTheme()->where('code', 'login')->where('locale', app()->getLocale())->first();
        $data = json_decode($page->data, true);

        return view('frontend::auth.login', ['data' => $data]);
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        // if (setting('otp_verification', 'permission')) {
        //     $user = $request->user();
        //     $otp = random_int(1000, 9999);

        //     $user->update([
        //         'otp' => $otp,
        //     ]);
        // }

        LoginActivities::add();

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
