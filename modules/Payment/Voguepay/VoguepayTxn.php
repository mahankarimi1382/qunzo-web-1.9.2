<?php

namespace Payment\Voguepay;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Payment\Transaction\BaseTxn;

class VoguepayTxn extends BaseTxn
{
    protected $merchantId;

    public function __construct($txnInfo)
    {
        parent::__construct($txnInfo);
        $this->merchantId = gateway_info('voguepay')->merchant_id;
    }

    public function deposit()
    {
        $info = [
            'merchant_id' => $this->merchantId,
            'email' => Auth::user()->email,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'success_url' => route('status.success', ['reftrn' => Crypt::encryptString($this->txn)]),
        ];

        return Http::post('https://pay.voguepay.com', $info);
    }
}
