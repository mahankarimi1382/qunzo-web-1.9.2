<?php

namespace App\Services;

use App\Enums\InvoiceType;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Resources\PaymentLinkResource;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentLinkService
{
    use ApiResponseTrait;

    public function validate(Request $request): bool|\Illuminate\Validation\ValidationException
    {
        $user = $request->user();
        $currencies = array_merge(Currency::pluck('code')->toArray(), [setting('site_currency', 'global')]);

        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric',
            'currency' => ['nullable', 'string', Rule::in($currencies)],
            'notes' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return makeValidationException($validator->errors()->toArray());
        }

        // KYC Verification
        if (! $user->isKycVerified()) {
            return makeValidationException([
                'kyc_payment' => [__('Please verify your KYC.')],
            ]);
        }

        return true;
    }

    public function createPaymentLink(Request $request): PaymentLinkResource|Exception
    {
        try {
            $paymentLink = DB::transaction(function () use ($request) {
                $paymentLink = Invoice::create([
                    'user_id' => $request->user()->id,
                    'currency' => $request->currency,
                    'type' => InvoiceType::PaymentLink,
                    'issue_date' => now(),
                    'address' => $request->notes,
                    'amount' => $request->amount,
                    'charge' => 0,
                    'total_amount' => $request->amount,
                    'is_paid' => false,
                    'is_published' => true,
                ]);

                // Create Transaction
                Transaction::create([
                    'user_id' => $request->user()->id,
                    'invoice_id' => $paymentLink->id,
                    'description' => 'Payment Link #'.$paymentLink->number,
                    'type' => TxnType::PaymentLink,
                    'amount' => $request->float('amount', 0),
                    'charge' => 0,
                    'final_amount' => $request->float('amount', 0),
                    'wallet_type' => $paymentLink->currency == setting('site_currency', 'global') ? 'default' : $paymentLink->wallet?->id,
                    'pay_currency' => $paymentLink->currency,
                    'status' => TxnStatus::Pending,
                    'method' => 'Payment Link',
                ]);

                return $paymentLink;
            });

            return new PaymentLinkResource($paymentLink);
        } catch (\Throwable $th) {
            Log::error('Payment link creation error: '.$th->getMessage());
            throw new Exception('Sorry, something went wrong!');
        }
    }
}
