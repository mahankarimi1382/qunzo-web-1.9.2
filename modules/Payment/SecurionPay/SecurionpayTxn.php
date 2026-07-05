<?php

namespace Payment\Securionpay;

use App\Traits\Payment;
use Illuminate\Support\Facades\Crypt;
use Payment\Transaction\BaseTxn;
use SecurionPay\Exception\SecurionPayException;
use SecurionPay\SecurionPayGateway;

class SecurionpayTxn extends BaseTxn
{
    use Payment;

    protected $secretKey;

    public function __construct($txnInfo)
    {
        parent::__construct($txnInfo);

        $credentials = gateway_info('securionpay');
        $this->secretKey = $credentials->secret_key;
    }

    public function deposit()
    {
        return to_route('non-hosted-gateway', ['gateway' => 'securionpay', 'tnx' => $this->txn]);
    }

    public function nonHostedPayment($request)
    {
        $gateway = new SecurionPayGateway($this->secretKey);

        try {
            $charge = $gateway->retrieveCharge($request->securionpayChargeId);

            if ($charge->getStatus() == 'successful' && $charge->getAmount() == (int) round($this->final_amount * 100)) {
                return self::paymentSuccess($this->txn);
            }

            return to_route('status.cancel', ['reftrn' => Crypt::encryptString($this->txn)]);
        } catch (SecurionPayException $e) {
            return to_route('status.cancel', ['reftrn' => Crypt::encryptString($this->txn)]);
        }
    }
}
