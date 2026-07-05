<?php

namespace Payment\Coingate;

use Illuminate\Support\Facades\Crypt;
use Payment\Transaction\BaseTxn;

class CoingateTxn extends BaseTxn
{
    protected $apiKey;

    public function __construct($txnInfo)
    {
        parent::__construct($txnInfo);
        $this->apiKey = gateway_info('coingate')->api_token;
    }

    public function deposit()
    {
        $client = new \CoinGate\Client($this->apiKey, true);

        $params = [
            'order_id' => $this->txn,
            'price_amount' => $this->amount,
            'price_currency' => $this->currency,
            'receive_currency' => 'EUR',
            'callback_url' => route('ipn.coingate'),
            'cancel_url' => route('status.cancel', ['reftrn' => Crypt::encryptString($this->txn)]),
            'success_url' => route('status.success', ['reftrn' => Crypt::encryptString($this->txn)]),
            'title' => $this->siteName,
            'description' => '',
        ];

        $status = $client->order->create($params);

        return redirect()->to($status->payment_url);
    }
}
