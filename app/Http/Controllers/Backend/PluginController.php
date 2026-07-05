<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PluginController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:plugin-setting'),
        ];
    }

    public function plugin()
    {
        $plugins = Plugin::get();

        return view('backend.setting.plugin.index', ['plugins' => $plugins]);
    }

    public function pluginData($id)
    {
        $plugin = Plugin::find($id);

        return view('backend.setting.plugin.include.__plugin_data', ['plugin' => $plugin])->render();
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $plugin = Plugin::find($id);
            $status = (bool) $request->status;

            if ($plugin->type == 'sms' && $status) {
                Plugin::where('type', 'sms')->update([
                    'status' => 0,
                ]);
            }

            $pluginOldData = json_decode($plugin->data, true);
            $requestData = $request->data;

            if ($request->hasFile('data.upload_account_json')) {
                $file = $request->file('data.upload_account_json');
                $requestData['upload_account_json'] = self::fileUpload($file, $pluginOldData['upload_account_json'] ?? null);
            }

            $plugin->update([
                'data' => json_encode($requestData),
                'status' => $status,
            ]);

            DB::commit();

            $status = 'success';
            $message = __('Settings has been saved');
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }
}
