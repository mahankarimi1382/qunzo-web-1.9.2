<?php

namespace App\Http\Controllers\Backend;

use App\Enums\GatewayType;
use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:automatic-gateway-manage', ['only' => ['automatic', 'update', 'gatewayCurrency']]),
        ];
    }

    public function automatic(Request $request)
    {
        $gateways = Gateway::when($request->search != null, function ($query) {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })->get();

        return view('backend.automatic_gateway.index', ['gateways' => $gateways]);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
            'credentials' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $gateway = Gateway::find($id);

            $user = Auth::user();
            if ($gateway->type == GatewayType::Automatic) {
                if (! $user->can('automatic-gateway-manage')) {
                    return redirect()->route('admin.gateway.automatic');
                }
            } elseif (! $user->can('manual-gateway-manage')) {
                return redirect()->route('admin.gateway.manual');
            }

            $data = [
                'name' => $request->name,
                'status' => $request->status,
                'credentials' => json_encode($request->credentials),
            ];

            if ($request->hasFile('logo')) {
                $logo = self::imageUploadTrait($request->logo, $gateway->logo, 'gateway');
                $data = array_merge($data, ['logo' => $logo]);
            }

            $gateway->update($data);

            $status = 'success';
            $message = $gateway->name.' '.__('gateway updated successfully!');

            notify()->$status($message, $status);

            return redirect()->route('admin.gateway.automatic');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }

    public function gatewayCurrency($gateway_id)
    {
        $gateway = Gateway::find($gateway_id);
        $supportedCurrencies = $gateway->supported_currencies;

        return [
            'view' => view('backend.automatic_gateway.include.__supported_currency', ['supportedCurrencies' => $supportedCurrencies])->render(),
            'pay_currency' => $gateway->gateway_code,
        ];
    }
}
