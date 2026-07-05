<?php

namespace App\Services;

use App\Enums\InvoiceType;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceService
{
    use NotifyTrait;

    public function createInvoice($request)
    {
        $user = $request->user();

        try {

            if (isValidationException($checkLimit = $this->checkDailyLimit($user))) {
                return $checkLimit;
            }

            $calculationData = $this->calculateInvoiceTotals($request);

            DB::beginTransaction();

            $invoice = $this->createInvoiceRecord($user, $request, $calculationData);

            $transaction = $this->createInvoiceTransaction($user, $invoice, $calculationData);

            if ($request->boolean('is_published')) {
                $this->sendInvoiceNotification($invoice);
            }

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function updateInvoice($request, $invoiceId)
    {
        $user = $request->user();

        try {

            if (isValidationException($calculationData = $this->calculateInvoiceTotals($request))) {
                return $calculationData;
            }

            DB::beginTransaction();

            $invoice = $this->updateInvoiceRecord($user, $invoiceId, $request, $calculationData);
            if (isValidationException($invoice)) {
                return $invoice;
            }

            $this->updateInvoiceTransaction($invoice, $calculationData, $request);

            if ($request->boolean('is_published')) {
                $this->sendInvoiceNotification($invoice);
            }

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function payInvoice($request, $invoiceId, $isApi = false)
    {
        $user = $request->user();

        try {
            $invoice = Invoice::where('is_published', true)->findOrFail($invoiceId);

            if ($invoice->is_paid) {
                return makeValidationException([
                    'invoice' => [__('Invoice is already paid')],
                ]);
            }

            $wallet = null;
            if ($request->wallet_id && $request->wallet_id !== 'default') {
                $wallet = UserWallet::where('user_id', $user->id)->where('id', $request->wallet_id)->first();
                if (! $wallet) {
                    return makeValidationException([
                        'wallet_id' => [__('Invalid wallet selected')],
                    ]);
                }
            }

            $this->validateInvoicePayment($invoice, $wallet, $user);

            DB::beginTransaction();

            $paymentTransaction = $this->processInvoicePayment($user, $invoice, $wallet);

            $invoice->update(['is_paid' => true]);

            Transaction::where('invoice_id', $invoice->id)->update([
                'status' => TxnStatus::Success,
            ]);

            $this->sendPaymentNotification($user, $invoice, $paymentTransaction);

            DB::commit();

            if ($isApi) {
                return [
                    'success' => true,
                    'invoice' => $invoice,
                    'transaction' => $paymentTransaction,
                    'message' => __('Invoice paid successfully!'),
                ];
            }

            return $paymentTransaction;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isApi) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }

            throw $e;
        }
    }

    private function checkDailyLimit($user)
    {
        $todayInvoices = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', TxnType::Invoice)
            ->whereDate('created_at', now())
            ->count();

        if ($todayInvoices >= setting('invoice_daily_limit', 'invoice')) {
            return makeValidationException([
                'invoice_daily_limit' => [__('You have reached your daily invoice limit!')],
            ]);
        }
    }

    private function calculateInvoiceTotals($request)
    {
        $subTotal = 0;
        $items = [];
        $chargeCurrency = setting('site_currency', 'global');
        $currencyData = Currency::query()->where('code', $request->currency)->first();

        if (! $currencyData && $request->currency != $chargeCurrency) {
            throw makeValidationException([
                'currency' => [__('Invalid currency')],
            ]);
        }

        foreach ($request->items as $key => $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $subTotal += $itemSubtotal;
            $item['subtotal'] = $itemSubtotal;
            $items[$key] = $item;
        }

        $invoiceCharge = setting('invoice_charge', 'invoice');
        $invoiceChargeType = setting('invoice_charge_type', 'invoice');

        if ($invoiceChargeType === 'percentage') {
            $charge = ($invoiceCharge * $subTotal) / 100;
        } else {
            $charge = $chargeCurrency == $request->currency
                ? $invoiceCharge
                : $invoiceCharge * $currencyData->conversion_rate;
        }

        return [
            'subTotal' => $subTotal,
            'charge' => $charge,
            'totalAmount' => $subTotal + $charge,
            'items' => $items,
            'currency' => $currencyData,
        ];
    }

    private function createInvoiceRecord($user, $request, $calculationData)
    {
        return Invoice::create([
            'user_id' => $user->id,
            'to' => $request->to,
            'email' => $request->email,
            'address' => $request->address,
            'currency' => $request->currency,
            'type' => InvoiceType::Invoice,
            'issue_date' => $request->date('issue_date'),
            'items' => $calculationData['items'],
            'charge' => $calculationData['charge'],
            'amount' => $calculationData['subTotal'],
            'total_amount' => $calculationData['totalAmount'],
            'is_paid' => false,
            'is_published' => $request->boolean('is_published'),
        ]);
    }

    private function updateInvoiceRecord($user, $invoiceId, $request, $calculationData)
    {
        $invoice = Invoice::where('user_id', $user->id)->find($invoiceId);

        if (! $invoice) {
            return makeValidationException([
                'invoice' => [__('Invoice not found')],
            ]);
        }

        // Update invoice
        $invoice->update([
            'to' => $request->to,
            'email' => $request->email,
            'address' => $request->address,
            'currency' => $request->currency,
            'issue_date' => $request->date('issue_date'),
            'items' => $calculationData['items'],
            'charge' => $calculationData['charge'],
            'amount' => $calculationData['subTotal'],
            'total_amount' => $calculationData['totalAmount'],
            'is_paid' => $request->boolean('is_paid'),
            'is_published' => $request->boolean('is_published'),
        ]);

        // Update transaction
        $invoice->transaction->update([
            'amount' => $calculationData['subTotal'],
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['totalAmount'],
            'pay_currency' => $invoice->currency,
            'wallet_type' => $invoice->currency == setting('site_currency', 'global') ? 'default' : $invoice->wallet->id,
        ]);

        return $invoice;
    }

    private function createInvoiceTransaction($user, $invoice, $calculationData)
    {
        return Transaction::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'description' => 'Invoice issued #'.$invoice->number,
            'type' => TxnType::Invoice,
            'amount' => $calculationData['subTotal'],
            'charge' => $calculationData['charge'],
            'final_amount' => $calculationData['totalAmount'],
            'pay_currency' => $invoice->currency,
            'wallet_type' => $invoice->currency == setting('site_currency', 'global') ? 'default' : $invoice->wallet->id,
            'status' => TxnStatus::Pending,
            'method' => 'Invoice',
        ]);
    }

    private function updateInvoiceTransaction($invoice, $calculationData, $request)
    {
        Transaction::where('invoice_id', $invoice->id)->update([
            'amount' => $calculationData['subTotal'],
            'charge' => $calculationData['charge'],
            'pay_currency' => $invoice->currency,
            'wallet_type' => $invoice->currency == setting('site_currency', 'global') ? 'default' : $invoice->wallet->id,
            'final_amount' => $calculationData['totalAmount'],
            'status' => $request->boolean('is_paid') ? TxnStatus::Success : TxnStatus::Pending,
        ]);
    }

    private function validateInvoicePayment($invoice, $wallet, $user)
    {
        $amount = $invoice->total_amount;
        $isDefaultWallet = ! $wallet;

        if ($isDefaultWallet) {
            if ($user->balance < $amount) {
                return makeValidationException([
                    'wallet' => [__('Insufficient balance in default wallet!')],
                ]);
            }
        } else {
            if ($wallet->balance < $amount) {
                return makeValidationException([
                    'wallet' => [__('Insufficient balance in selected wallet!')],
                ]);
            }

            if ($wallet->currency->code !== $invoice->currency) {
                return makeValidationException([
                    'currency' => [__('Currency mismatch!')],
                ]);
            }
        }
    }

    private function processInvoicePayment($user, $invoice, $wallet)
    {
        $amount = $invoice->total_amount;
        $isDefaultWallet = ! $wallet;

        if ($isDefaultWallet) {
            $user->decrement('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }

        $invoiceOwner = User::find($invoice->user_id);
        if ($isDefaultWallet) {
            $invoiceOwner->increment('balance', $amount);
        } else {
            $ownerWallet = UserWallet::firstOrCreate([
                'user_id' => $invoice->user_id,
                'currency_id' => $wallet->currency_id,
            ], [
                'balance' => 0,
            ]);
            $ownerWallet->increment('balance', $amount);
        }

        return Transaction::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'description' => 'Invoice Payment #'.$invoice->number,
            'type' => TxnType::Payment,
            'amount' => $amount,
            'charge' => 0,
            'final_amount' => $amount,
            'pay_currency' => $invoice->currency,
            'wallet_type' => $wallet ? $wallet->id : 'default',
            'status' => TxnStatus::Success,
            'method' => 'Invoice Payment',
        ]);
    }

    private function sendInvoiceNotification($invoice)
    {
        $shortcodes = [
            '[[invoice_number]]' => $invoice->number,
            '[[invoice_to]]' => $invoice->to,
            '[[invoice_amount]]' => formatAmount($invoice->total_amount, $invoice->currency),
            '[[invoice_currency]]' => $invoice->currency,
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(
            $invoice->email,
            'invoice_created',
            'User',
            $shortcodes,
            null,
            null,
            ''
        );
    }

    private function sendPaymentNotification($user, $invoice, $transaction)
    {
        $shortcodes = [
            '[[invoice_number]]' => $invoice->number,
            '[[payer_name]]' => $user->full_name,
            '[[amount]]' => formatAmount($transaction->amount, $invoice->currency),
            '[[currency]]' => $invoice->currency,
            '[[payment_id]]' => $transaction->tnx,
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $invoiceOwner = User::find($invoice->user_id);
        $this->sendNotify(
            $invoiceOwner->email,
            'invoice_paid',
            'User',
            $shortcodes,
            $invoiceOwner->phone,
            $invoiceOwner->id,
            ''
        );
    }

    public function validate(Request $request)
    {

        if (! setting('user_invoice', 'permission')) {
            return makeValidationException([
                'user_invoice' => [__('Invoice feature is not enabled!')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'currency' => 'required|string',
            'issue_date' => 'required|date',
            'is_published' => 'required|boolean',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->all());
        }

        $currency = Currency::where('code', $request->currency)->where('status', 1)->first();
        if ($request->currency != setting('site_currency', 'global') && ! $currency) {
            return makeValidationException([
                'currency' => [__('Invalid currency')],
            ]);
        }

        return true;
    }
}
