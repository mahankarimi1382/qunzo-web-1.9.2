<?php

namespace App\Livewire\Payment;

use App\Enums\TxnStatus;
use App\Models\SandboxTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;

class SandboxPayment extends BasePaymentComponent
{
    #[Locked]
    public $redirectUrl = null;

    #[Locked]
    public bool $isSandbox = true;

    public function mount($transaction_id)
    {
        $this->transaction = SandboxTransaction::with('user.merchant')->where('tnx', $transaction_id)->firstOrFail();
        $this->checkPaymentStatus();
        $this->payAmount = $this->transaction->final_amount;
        $this->step = 2;

        $appRedirectUrl = route('status.success', ['reftrn' => Crypt::encryptString($this->transaction->tnx), 'is_app' => 'true']);

        $this->redirectUrl = $this->isApp() ? $appRedirectUrl : $this->transaction->callback_url;
    }

    public function nextStep()
    {
        // Get default credentials
        $credentials = $this->defaultCredentails();

        // Validate account number
        if ($this->account_number !== $credentials['account_number']) {
            $this->addError('account_number', __('UID is invalid!'));

            return;
        }

        $this->clearValidation('account_number');

        $this->step += 1;
    }

    public function payNow()
    {
        // Get default credentials
        $credentials = $this->defaultCredentails();

        // Check password is invalid return error
        if ($this->account_password !== $credentials['password']) {
            $this->addError('account_password', __('Password is invalid!'));
            $this->reset('account_password');

            return;
        }

        // Process payment
        try {

            DB::beginTransaction();

            // Process payment
            $this->processPayment();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError('wallet', 'Payment failed, please try again!');
            $this->resetPayment();
        }
    }

    private function processPayment(): void
    {
        // Update transaction status
        $this->transaction->update([
            'status' => TxnStatus::Success,
        ]);

        // Send request to IPN
        if ($ipnUrl = data_get($this->transaction->manual_field_data, 'ipn_url')) {
            $this->sendRequestToIpn($this->transaction, $ipnUrl);
        }

        $this->isSuccess = true;
        $this->isRedirection = $this->redirectUrl != null;
    }

    private function defaultCredentails(): array
    {
        return [
            'account_number' => '12344567890',
            'password' => '12345678',
        ];
    }
}
