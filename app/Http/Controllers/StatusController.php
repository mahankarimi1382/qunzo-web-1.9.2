<?php

namespace App\Http\Controllers;

use App\Enums\TxnStatus;
use App\Facades\Txn\Txn;
use App\Models\Transaction;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Uri;

class StatusController extends Controller
{
    use NotifyTrait, Payment;

    public function success(Request $request)
    {
        $depositTnx = Crypt::decryptString($request->reftrn);

        return self::paymentSuccess($depositTnx, false);
    }

    public function cancel(Request $request)
    {
        $ref = Crypt::decryptString($request->reftrn);

        (new Txn)->update($ref, TxnStatus::Failed);

        $txnInfo = Transaction::tnx($ref);

        if ($txnInfo->callback_url !== null) {
            $callbackUrl = $txnInfo->callback_url;

            $modifiedUrl = Uri::of($callbackUrl)->withQuery([
                'tnx' => $txnInfo->tnx,
            ]);

            return redirect()->away($modifiedUrl);
        }

        return response()->json([
            'status' => TxnStatus::Failed,
            'message' => 'Transaction Canceled',
        ]);
    }

    public function ownSuccess($ref)
    {
        $txnInfo = Transaction::tnx($ref);

        $response = 'failed';

        if ($txnInfo->status == TxnStatus::Success) {
            $response = 'success';
        }

        return response()->json([
            'status' => $response,
            'data' => $txnInfo->toArray(),
        ]);
    }
}
