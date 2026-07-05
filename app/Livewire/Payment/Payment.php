<?php

namespace App\Livewire\Payment;

use App\Enums\InvoiceType;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Currency;
use App\Models\DepositMethod;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use App\Traits\Payment as PaymentTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('frontend::payment.layout')]
class Payment extends BasePaymentComponent
{
    use NotifyTrait, PaymentTrait;

    #[Locked]
    public $redirectUrl = null;

    public function mount($transaction_id)
    {
        $this->transaction = Transaction::with('user.merchant')->where('tnx', $transaction_id)->firstOrFail();
        $this->checkPaymentStatus();
        $this->payAmount = $this->transaction->final_amount;

        if (! $this->isEmptyPaymentLink()) {
            $this->step = 2;
        }

        $appRedirectUrl = route('payment.success', ['reftrn' => $this->transaction->tnx]);

        $this->redirectUrl = $this->isApp() ? $appRedirectUrl : $this->transaction->callback_url ?? request()->get('redirect_url');
    }

    public function nextStep()
    {
        $step = $this->step;

        if ($step === 1) {

            $this->validate([
                'link_amount' => 'required|numeric',
                'wallet_id' => 'required',
            ], [
                'link_amount.required' => 'Amount is required!',
                'link_amount.numeric' => 'Amount must be numeric!',
                'wallet_id.required' => 'Currency is required!',
            ]);

            $targetCurrency = $this->wallet_id === 'default' ? setting('site_currency', 'global') : Currency::query()->findOrFail($this->wallet_id)->code;

            $this->transaction->update([
                'pay_currency' => $targetCurrency,
                'wallet_type' => $this->wallet_id,
                'amount' => $this->link_amount,
                'final_amount' => $this->link_amount,
            ]);

            // Update Invoice
            if (($this->transaction->invoice?->type == InvoiceType::PaymentLink && $this->transaction->type == TxnType::PaymentLink) && $this->isEmptyPaymentLink()) {
                $this->transaction->invoice->update([
                    'currency' => $targetCurrency,
                    'amount' => $this->link_amount,
                    'total_amount' => $this->link_amount,
                ]);
            }
        } elseif ($step === 2) {
            if ($this->payment_type === 'wallet') {
                $this->validate([
                    'account_number' => 'required|exists:users',
                ], [
                    'account_number.required' => 'UID is required!',
                    'account_number.exists' => 'UID is invalid!',
                ]);
            } elseif ($this->payment_type === 'gateway') {
                $this->validate([
                    'gateway_id' => 'required|exists:deposit_methods,id',
                ], [
                    'gateway_id.required' => 'Please select a gateway!',
                    'gateway_id.exists' => 'Selected gateway is invalid!',
                ]);

                // For gateway payments, process immediately without password step
                return $this->processGatewayPayment();
            }
        }

        // Next step
        $this->step++;
    }

    public function payNow()
    {
        if ($this->payment_type === 'gateway') {
            return $this->processGatewayPayment();
        }

        // Wallet payment flow (existing logic)
        $this->validate([
            'account_password' => 'required|min:8',
        ], [
            'account_password.required' => 'Password is required!',
            'account_password.min' => 'Password must be at least 8 characters!',
        ]);

        $siteCurrency = setting('site_currency');
        $payCurrency = $this->transaction->pay_currency;
        $user = User::where('account_number', $this->account_number)->first();
        $existsWalletOnUser = UserWallet::where('user_id', $user->id)->whereRelation('currency', 'code', $payCurrency)->first();

        // Check password is invalid return error
        if (! Hash::check($this->account_password, $user->password)) {
            $this->addError('account_password', 'Password is invalid!');
            $this->reset('account_password');

            return;
        }

        // Checking payment currency wallet exists on user wallet
        if (! $existsWalletOnUser && $siteCurrency != $payCurrency) {
            $this->addError('wallet', 'Payment currency not found in this account!');
            // Reset payment
            $this->resetPayment();

            return;
        }

        // Check balance
        if (! $this->hasSufficientBalance($user, $payCurrency)) {
            // Add error message
            $this->addError('wallet', 'Insufficient funds!');
            // Reset payment
            $this->resetPayment();

            return;
        }

        // Process payment
        try {

            DB::beginTransaction();

            // Update user balance
            if ($siteCurrency == $payCurrency) {
                $user->decrement('balance', $this->payAmount);
            } else {
                $existsWalletOnUser->decrement('balance', $this->payAmount);
            }

            // Create transaction for payment user
            $description = match ($this->transaction->type) {
                TxnType::Invoice => 'Invoice Payment #'.$this->transaction->invoice->number,
                TxnType::PaymentLink => 'Payment To #'.$this->transaction->invoice->number,
                default => 'Make Payment Via Gateway',
            };

            Transaction::create([
                'user_id' => $user->id,
                'description' => $description,
                'type' => TxnType::Payment,
                'invoice_id' => $this->transaction->invoice_id,
                'amount' => $this->transaction->amount,
                'pay_currency' => $payCurrency,
                'wallet_type' => $siteCurrency === $payCurrency ? 'default' : $existsWalletOnUser->id,
                'charge' => $this->transaction->charge,
                'final_amount' => $this->payAmount,
                'method' => 'Gateway',
                'status' => TxnStatus::Success,
            ]);

            // Update transaction for receiver user
            $this->transaction->update([
                'from_user_id' => $user->id,
                'status' => TxnStatus::Success,
            ]);

            // Update receiver user balance
            if ($siteCurrency === $payCurrency) {
                $this->transaction->user->increment('balance', $this->transaction->amount);
            } else {
                $this->transaction->user->wallets()->where('currency_id', $existsWalletOnUser->currency_id)->increment('balance', $this->transaction->amount);
            }

            // Process payment
            $this->processPayment($user, $existsWalletOnUser);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Payment gateway error: '.$exception->getMessage());
            $this->markAsFailed();
            $this->resetPayment();
            $this->addError('wallet', 'Payment failed, please try again!');
        }
    }

    private function processGatewayPayment()
    {
        $this->validate([
            'gateway_id' => 'required|exists:deposit_methods,id',
        ], [
            'gateway_id.required' => 'Please select a gateway!',
            'gateway_id.exists' => 'Selected gateway is invalid!',
        ]);

        try {
            $depositMethod = DepositMethod::with('gateway')->where('type', 'auto')->findOrFail($this->gateway_id);
            $payCurrency = $this->transaction->pay_currency;

            if ($depositMethod->currency !== $payCurrency) {
                $this->addError('gateway_id', 'Selected gateway does not support the payment currency!');

                return;
            }

            if (! $depositMethod->gateway || $depositMethod->gateway->status != 1) {
                $this->addError('gateway_id', 'Gateway is not available!');

                return;
            }

            DB::beginTransaction();

            // Update existing transaction for gateway payment
            $this->transaction->update([
                'method' => $depositMethod->name,
                'status' => TxnStatus::Pending,
                'callback_url' => $this->redirectUrl ?? route('pay', ['transaction_id' => $this->transaction->tnx]),
                'manual_field_data' => array_merge($this->transaction->manual_field_data ?? [], [
                    'deposit_method_id' => $depositMethod->id,
                    'gateway_code' => $depositMethod->gateway_code,
                ]),
            ]);

            DB::commit();

            // Redirect to gateway using existing transaction
            $redirectResponse = $this->paymentAutoGateway($depositMethod->gateway->gateway_code, $this->transaction);

            if ($redirectResponse) {
                return $redirectResponse;
            }

            DB::rollBack();
            $this->addError('gateway_id', 'Failed to initialize gateway payment. Please try again!');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Gateway payment error: '.$exception->getMessage());
            $this->addError('gateway_id', 'Payment processing failed. Please try again!');
        }
    }

    private function markAsFailed(): void
    {
        $this->transaction->update([
            'status' => TxnStatus::Failed,
        ]);
    }

    public function isEmptyPaymentLink(): bool
    {
        if ($this->transaction->invoice?->type != InvoiceType::PaymentLink || $this->transaction->type != TxnType::PaymentLink) {
            return false;
        }

        return $this->transaction->type == TxnType::PaymentLink && $this->transaction->invoice?->amount == null;
    }

    private function hasSufficientBalance(User $user, $payCurrency): bool
    {
        $existsWalletOnUser = UserWallet::where('user_id', $user->id)
            ->whereRelation('currency', 'code', $payCurrency)
            ->first();
        $siteCurrency = setting('site_currency');

        $isSameCurrency = $siteCurrency === $payCurrency;
        $isDifferentCurrency = $siteCurrency !== $payCurrency;

        return ! ($isSameCurrency && $user->balance < $this->payAmount) && ! ($isDifferentCurrency && $existsWalletOnUser?->balance < $this->payAmount);
    }

    private function processPayment(User $user, $wallet): void
    {
        $merchant = $this->transaction->user->merchant;

        // Update invoice status
        if ($this->transaction->type == TxnType::Invoice || $this->transaction->type == TxnType::PaymentLink) {
            $this->transaction->invoice->markAsPaid();
        }

        // Referral Bonus
        $this->processReferral($user, $wallet);

        // Update transaction status
        $this->transaction->update([
            'status' => TxnStatus::Success,
        ]);

        // Send request to IPN
        if ($ipnUrl = data_get($this->transaction->manual_field_data, 'ipn_url')) {
            $this->sendRequestToIpn($this->transaction, $ipnUrl);
        }

        // Send notification
        $this->sendNotification($merchant, $user);

        $this->isSuccess = true;
        $this->isRedirection = $this->redirectUrl != null;
    }

    private function processReferral($user, $wallet): void
    {
        if ($this->transaction->type == TxnType::Invoice && setting('invoice_pay', 'referral_level')) {
            $level = LevelReferral::where('type', 'invoice_pay')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus($user, 'invoice_pay', $this->transaction->amount, $level, 1, null, $wallet);
        } elseif ($this->transaction->type == TxnType::Payment && setting('payment', 'referral_level')) {
            $level = LevelReferral::where('type', 'payment')->max('the_order') + 1;
            creditCurrencyWiseReferralBonus($user, 'payment', $this->transaction->amount, $level, 1, null, $wallet);
        }
    }

    private function sendNotification($merchant, $user)
    {
        if ($this->transaction->type == TxnType::Payment) {
            $shortcodes = [
                '[[merchant_name]]' => $merchant->full_name,
                '[[amount]]' => formatAmount($this->transaction->amount, $this->transaction->currency),
                '[[charge]]' => formatAmount($this->transaction->charge, $this->transaction->currency),
                '[[total_amount]]' => formatAmount($this->transaction->final_amount, $this->transaction->currency),
                '[[wallet]]' => data_get($this->transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                '[[gateway]]' => $this->transaction->method,
                '[[payment_at]]' => $this->transaction->created_at,
                '[[payment_id]]' => $this->transaction->tnx,
                '[[user_name]]' => $user->full_name,
                '[[user_account_no]]' => $user->account_number,
                '[[site_title]]' => setting('site_title', 'global'),
                '[[currency]]' => data_get($this->transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
            ];

            $this->sendNotify($merchant->email, 'merchant_payment', 'Merchant', $shortcodes, $merchant->phone, $merchant->id, '#');
        } elseif ($this->transaction->type == TxnType::Invoice) {
            $shortcodes = [
                '[[full_name]]' => $user->full_name,
                '[[invoice_number]]' => $this->transaction->invoice->number,
                '[[amount]]' => formatAmount($this->transaction->amount, $this->transaction->currency),
                '[[charge]]' => formatAmount($this->transaction->charge, $this->transaction->currency),
                '[[total_amount]]' => formatAmount($this->transaction->final_amount, $this->transaction->currency),
                '[[invoice_link]]' => '#',
                '[[currency]]' => data_get($this->transaction->userWallet, 'currency.code', setting('site_currency', 'global')),
                '[[site_title]]' => setting('site_title', 'global'),
            ];

            $this->sendNotify($user->email, 'user_invoice_payment', 'User', $shortcodes, $user->phone, $user->id, '#');
        }
    }
}
