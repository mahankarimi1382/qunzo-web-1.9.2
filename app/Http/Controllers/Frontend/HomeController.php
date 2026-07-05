<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function languageUpdate($locale)
    {
        session()->put('locale', $locale);

        return redirect()->back();
    }

    public function session(Request $request)
    {
        $key = $request->input('key');

        $value = $request->input('value');

        session([$key => $value]);

        return response()->json(['success' => true]);
    }

    public function subscribeNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:subscribers'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        Subscriber::create([
            'email' => $request->email,
        ]);

        notify()->success(__('Subscribed Successfully'));

        return redirect()->back();
    }

    public function nonHostedGateway($gateway, $tnx)
    {
        $txnInfo = Transaction::tnx($tnx);
        return view('gateway.' . $gateway, compact('txnInfo'));
    }
}
