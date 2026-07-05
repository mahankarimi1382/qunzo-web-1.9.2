<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:site-theme-manage', ['only' => ['siteTheme', 'statusUpdate']]),
        ];
    }

    public function siteTheme()
    {
        $themes = Theme::where('type', 'site')->get();

        return view('backend.theme.site', ['themes' => $themes]);
    }

    public function statusUpdate(Request $request)
    {
        $theme = Theme::find($request->id);

        $status = $theme->type == 'site' ? 1 : $request->status;

        if ($status) {
            $query = Theme::where('type', $theme->type)->where('status', true);
            $oldStatus = $query->pluck('id')->toArray();
            $query->update([
                'status' => 0,
            ]);
        }

        $theme->update([
            'status' => $status,
        ]);

        Cache::forget('landingSections');
        Cache::forget('pages');

        if ($theme->type == 'site') {
            notify()->success(__('Site Theme Status Updated Successfully'));

            return back();
        }

        return response()->json([
            'old_status' => $oldStatus ?? [],
            'message' => __('Landing Theme Status Updated Successfully'),
        ]);
    }
}
