<?php

namespace App\Traits;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\DepositMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Uri;
use Payment\Binance\BinanceTxn;
use Payment\Blockchain\BlockchainTxn;
use Payment\BlockIo\BlockIoTxn;
use Payment\Btcpayserver\BtcpayserverTxn;
use Payment\Cashmaal\CashmaalTxn;
use Payment\Coinbase\CoinbaseTxn;
use Payment\Coingate\CoingateTxn;
use Payment\Coinpayments\CoinpaymentsTxn;
use Payment\Coinremitter\CoinremitterTxn;
use Payment\Cryptomus\CryptomusTxn;
use Payment\Flutterwave\FlutterwaveTxn;
use Payment\Instamojo\InstamojoTxn;
use Payment\Mollie\MollieTxn;
use Payment\Monnify\MonnifyTxn;
use Payment\Nowpayments\NowpaymentsTxn;
use Payment\Paymongo\PaymongoTxn;
use Payment\Paypal\PaypalTxn;
use Payment\Paytm\PaytmTxn;
use Payment\Perfectmoney\PerfectmoneyTxn;
use Payment\Razorpay\RazorpayTxn;
use Payment\Securionpay\SecurionpayTxn;
use Payment\Stripe\StripeTxn;
use Payment\Twocheckout\TwocheckoutTxn;

trait Payment
{
    protected function depositAutoGateway($gateway, $txnInfo)
    {
        $txn = $txnInfo->tnx;
        Session::put('deposit_tnx', $txn);
        $gateway = DepositMethod::code($gateway)->first()->gateway->gateway_code ?? 'none';

        $gatewayTxn = self::gatewayMap($gateway, $txnInfo);

        if ($gatewayTxn) {
            return $gatewayTxn->deposit();
        }
    }

    protected function paymentAutoGateway($gatewayCode, $txnInfo)
    {
        $txn = $txnInfo->tnx;
        Session::put('deposit_tnx', $txn);

        $gatewayTxn = self::gatewayMap($gatewayCode, $txnInfo);

        if ($gatewayTxn) {
            return $gatewayTxn->deposit();
        }

        return false;
    }

    protected function withdrawAutoGateway($gatewayCode, $txnInfo)
    {
        $gatewayTxn = self::gatewayMap($gatewayCode, $txnInfo);
        if ($gatewayTxn && config('app.demo') == 0) {
            $gatewayTxn->withdraw();
        }
    }

    protected function paymentSuccess($ref)
    {
        $txnInfo = Transaction::whereIn('type', [TxnType::Deposit, TxnType::Payment, TxnType::Invoice, TxnType::PaymentLink])->tnx($ref);

        if (! $txnInfo) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($txnInfo->status !== TxnStatus::Success) {
            $txnInfo->update([
                'status' => TxnStatus::Success,
            ]);

            (new Txn)->update($ref, TxnStatus::Success, $txnInfo->user_id);

            if ($txnInfo->type !== TxnType::Deposit) {
                $this->handleDepositCompletion($txnInfo);
            }
        }

        if ($txnInfo->callback_url !== null) {

            $callbackUrl = $txnInfo->callback_url;

            $modifiedUrl = Uri::of($callbackUrl)->withQuery([
                'tnx' => $txnInfo->tnx,
            ]);

            return redirect()->away($modifiedUrl); // Redirect to the callback URL
        }

        return response()->json([
            'status' => $txnInfo->status,
            'type' => $txnInfo->type,
            'message' => $txnInfo->status == TxnStatus::Success ? 'Payment successful' : ($txnInfo->status == TxnStatus::Failed ? 'Payment failed' : 'Payment pending'),
            'amount' => $txnInfo->amount,
            'charge' => $txnInfo->charge,
            'pay_currency' => $txnInfo->pay_currency,
            'tnx' => $txnInfo->tnx,
        ]);
    }

    protected function handlePaymentCompletion($originalTxn, $gatewayTxn)
    {
        $component = new \App\Livewire\Payment\BasePaymentComponent;
        $component->processPaymentCompletion($originalTxn, $gatewayTxn);
    }

    protected function gatewayMap($gateway, $txnInfo)
    {
        $gatewayMap = [
            'paypal' => PaypalTxn::class,
            'stripe' => StripeTxn::class,
            'mollie' => MollieTxn::class,
            'perfectmoney' => PerfectmoneyTxn::class,
            'coinbase' => CoinbaseTxn::class,
            'paystack' => PaytmTxn::class,
            'voguepay' => BinanceTxn::class,
            'flutterwave' => FlutterwaveTxn::class,
            'cryptomus' => CryptomusTxn::class,
            'nowpayments' => NowpaymentsTxn::class,
            'securionpay' => SecurionpayTxn::class,
            'coingate' => CoingateTxn::class,
            'monnify' => MonnifyTxn::class,
            'coinpayments' => CoinpaymentsTxn::class,
            'paymongo' => PaymongoTxn::class,
            'coinremitter' => CoinremitterTxn::class,
            'btcpayserver' => BtcpayserverTxn::class,
            'binance' => BinanceTxn::class,
            'cashmaal' => CashmaalTxn::class,
            'blockio' => BlockIoTxn::class,
            'blockchain' => BlockchainTxn::class,
            'instamojo' => InstamojoTxn::class,
            'paytm' => PaytmTxn::class,
            'razorpay' => RazorpayTxn::class,
            'twocheckout' => TwocheckoutTxn::class,
        ];

        if (array_key_exists($gateway, $gatewayMap)) {
            return app($gatewayMap[$gateway], ['txnInfo' => $txnInfo]);
        }

        return false;
    }
}
