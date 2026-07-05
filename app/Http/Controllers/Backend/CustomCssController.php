<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CustomCss;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomCssController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:custom-css'),
        ];
    }

    public function customCss()
    {
        $customCss = CustomCss::first()->css;

        return view('backend.css_manage.index', ['customCss' => $customCss]);
    }

    public function customCssUpdate(Request $request)
    {

        CustomCss::first()->update([
            'css' => $request->custom_css,
        ]);

        notify()->success(__('Css Update Successfully'));

        return back();
    }
}
