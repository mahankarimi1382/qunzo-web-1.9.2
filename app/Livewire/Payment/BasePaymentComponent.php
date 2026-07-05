<?php

namespace App\Livewire\Payment;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Currency;
use App\Models\DepositMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('frontend::payment.layout')]
class BasePaymentComponent extends Component
{
    #[Locked]
    public bool $isLoaded = false;

    #[Locked]
    public bool $isSandbox = false;

    #[Locked]
    public bool $isSuccess = false;

    #[Locked]
    public bool $isCancelled = false;

    #[Locked]
    public bool $isRedirection = false;

    #[Locked]
    public int $step = 1;

    #[Locked]
    public $transaction;

    #[Locked]
    public $payAmount = 0;

    // For Payment Link
    public $link_amount;

    public $wallet_id = 'default';

    // For payment method
    public $payment_type = 'wallet';

    public $gateway_id;

    // For Payment using own system account
    public $account_number = '';

    public $account_password = '';

    public function checkPaymentStatus()
    {
        if ($this->transaction->status == TxnStatus::Success) {
            $this->isSuccess = true;
        }
    }

    protected function isApp()
    {
        return isFromApp();
    }

    public function loadPay()
    {
        $this->isLoaded = true;
    }

    #[Computed(persist: true)]
    public function currencies()
    {
        return array_merge([
            [
                'id' => 'default',
                'name' => 'Main Wallet',
                'code' => setting('site_currency', 'global'),
                'symbol' => setting('currency_symbol', 'global'),
            ],
        ], Currency::query()->where('status', true)->get()->toArray());
    }

    #[Computed(persist: true)]
    public function gateways()
    {
        if (! $this->transaction || ! $this->transaction->pay_currency) {
            return [];
        }

        $payCurrency = $this->transaction->pay_currency;

        return DepositMethod::with('gateway')
            ->has('gateway')
            ->where('type', 'auto')
            ->where('status', 1)
            ->where('currency', strtoupper($payCurrency))
            ->get()
            ->map(function ($depositMethod) {
                return [
                    'id' => $depositMethod->id,
                    'name' => $depositMethod->name,
                    'gateway_code' => $depositMethod->gateway_code,
                    'gateway_id' => $depositMethod->gateway_id,
                    'logo' => $depositMethod->gateway_logo,
                ];
            })
            ->values()
            ->toArray();
    }

    protected function resetPayment()
    {
        $this->isSuccess = false;
        $this->isRedirection = false;
        $this->step = 2;

        // Reset account number and password
        $this->reset(['account_number', 'account_password']);
    }

    public function cancelPayment()
    {
        $this->isCancelled = true;
        // Reset payment
        $this->resetPayment();
    }

    protected function sendRequestToIpn($transaction, $url)
    {
        try {
            $transactionId = data_get($transaction->manual_field_data, 'transaction_id');
            $secretKey = $transaction->user?->merchant?->secret_key;
            $signature = hash_hmac(
                'sha256',
                $transactionId.$transaction->final_amount,
                $secretKey
            );

            Http::asForm()->withOptions([
                'verify' => false,
            ])->post($url, [
                'status' => 'success',
                'signature' => $signature,
                'data' => [
                    'amount' => $transaction->amount,
                    'charge' => $transaction->charge,
                    'total_amount' => $transaction->final_amount,
                    'transaction_id' => $transactionId,
                    'currency' => $transaction->pay_currency,
                    'customer_name' => data_get($transaction->manual_field_data, 'customer_name'),
                    'customer_email' => data_get($transaction->manual_field_data, 'customer_email'),
                ],
            ]);
        } catch (\Throwable $e) {
        }
    }

    public function processPaymentCompletion($originalTxn, $gatewayTxn)
    {
        try {
            DB::beginTransaction();

            $siteCurrency = setting('site_currency');
            $payCurrency = $originalTxn->pay_currency;
            $merchant = $originalTxn->user;

            // Update receiver user balance
            if ($siteCurrency == $payCurrency) {
                $merchant->increment('balance', $originalTxn->amount);
            } else {
                $merchant->wallets()
                    ->whereRelation('currency', 'code', $payCurrency)
                    ->increment('balance', $originalTxn->amount);
            }

            // Update invoice status
            if (($originalTxn->type == TxnType::Invoice || $originalTxn->type == TxnType::PaymentLink) && $originalTxn->invoice) {
                $originalTxn->invoice->markAsPaid();
            }

            // Send IPN notification
            if ($ipnUrl = data_get($originalTxn->manual_field_data, 'ipn_url')) {
                $this->sendRequestToIpn($gatewayTxn, $ipnUrl);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment completion error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $title = $this->isSuccess ? __('Payment Success') : __('Pay Now');

        return view('frontend::payment.checkout')->title($title);
    }
}
