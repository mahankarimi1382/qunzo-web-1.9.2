<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    use NotifyTrait;

    public function create(): View
    {
        $page = Page::currentTheme()->where('code', 'forgetpassword')->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::currentTheme()->where('code', 'forgetpassword')->where('locale', defaultLocale())->first();
        }

        $data = json_decode($page->data, true);

        return view('frontend::auth.forgot-password', ['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back()->with('error', __('Email or Username not found!'));
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $token = route('password.reset', ['token' => $token, 'email' => $request->email]);

        $shortcodes = [
            '[[token]]' => $token,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => '#',
        ];

        $this->sendNotify($request->email, 'forgot_password', 'User', $shortcodes, null, null, $token);

        return redirect()->back()->with('status', __('We have emailed your password reset link!'));
    }
}
