<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->currency?->name,
            'balance' => $this->balance,
            'formatted_balance' => formatAmount($this->balance, $this->currency),
            'code' => $this->currency?->code,
            'symbol' => $this->currency?->symbol,
            'conversion_rate' => $this->currency?->conversion_rate,
            'icon' => asset($this->currency?->icon),
            'is_default' => false,

            // Payment
            'payment_limit' => $this->when($request->has('payment'), fn () => [
                'min' => currency()->getConvertedAmount(setting('payment_minimum', 'make_payment'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('payment_maximum', 'make_payment'), $this->currency, true),
            ]),

            // Transfer
            'transfer_limit' => $this->when($request->has('transfer'), fn () => [
                'min' => currency()->getConvertedAmount(setting('transfer_minimum', 'transfer'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('transfer_maximum', 'transfer'), $this->currency, true),
            ]),

            // Request Money
            'request_money_limit' => $this->when($request->has('request_money'), fn () => [
                'min' => currency()->getConvertedAmount(setting('request_money_minimum', 'request_money'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('request_money_maximum', 'request_money'), $this->currency, true),
            ]),

            // Cashout
            'cashout_limit' => $this->when($request->has('cashout'), fn () => [
                'min' => currency()->getConvertedAmount(setting('cashout_minimum', 'cashout'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('cashout_maximum', 'cashout'), $this->currency, true),
            ]),

            // Cashin
            'cashin_limit' => $this->when($request->has('cashin'), fn () => [
                'min' => currency()->getConvertedAmount(setting('cashin_minimum', 'cashin'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('cashin_maximum', 'cashin'), $this->currency, true),
            ]),

            // Gift
            'gift_limit' => $this->when($request->has('gift'), fn () => [
                'min' => currency()->getConvertedAmount(setting('gift_minimum', 'gift'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('gift_maximum', 'gift'), $this->currency, true),
            ]),

            // Exchange
            'exchange_limit' => $this->when($request->has('exchange'), fn () => [
                'min' => currency()->getConvertedAmount(setting('exchange_minimum', 'exchange'), $this->currency, true),
                'max' => currency()->getConvertedAmount(setting('exchange_maximum', 'exchange'), $this->currency, true),
            ]),

            'is_crypto' => $this->currency?->type === CurrencyType::Crypto,
            'currency_id' => $this->currency?->id,
        ];
    }

    public function withDefaultWallet(Request $request)
    {
        $currency = setting('site_currency', 'global');
        $currency_symbol = setting('currency_symbol', 'global');
        $user = $request->user();

        $defaultWallet = [
            'id' => 0,
            'name' => __('Main Wallet'),
            'account_no' => $user->account_number,
            'balance' => $user->balance,
            'formatted_balance' => formatAmount($user->balance, $currency),
            'code' => $currency,
            'symbol' => $currency_symbol,
            'icon' => null,
            'is_default' => true,
            'is_crypto' => false,
            'currency_id' => 0,

            // Payment
            'payment_limit' => $this->when($request->has('payment'), fn () => [
                'min' => currency()->getConvertedAmount(setting('payment_minimum', 'make_payment'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('payment_maximum', 'make_payment'), $currency, true),
            ]),

            // Transfer
            'transfer_limit' => $this->when($request->has('transfer'), fn () => [
                'min' => currency()->getConvertedAmount(setting('transfer_minimum', 'transfer'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('transfer_maximum', 'transfer'), $currency, true),
            ]),

            // Request Money
            'request_money_limit' => $this->when($request->has('request_money'), fn () => [
                'min' => currency()->getConvertedAmount(setting('request_money_minimum', 'request_money'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('request_money_maximum', 'request_money'), $currency, true),
            ]),

            // Cashout
            'cashout_limit' => $this->when($request->has('cashout'), fn () => [
                'min' => currency()->getConvertedAmount(setting('cashout_minimum', 'cashout'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('cashout_maximum', 'cashout'), $currency, true),
            ]),

            // Cashin
            'cashin_limit' => $this->when($request->has('cashin'), fn () => [
                'min' => currency()->getConvertedAmount(setting('cashin_minimum', 'cashin'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('cashin_maximum', 'cashin'), $currency, true),
            ]),

            // Gift
            'gift_limit' => $this->when($request->has('gift'), fn () => [
                'min' => currency()->getConvertedAmount(setting('gift_minimum', 'gift'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('gift_maximum', 'gift'), $currency, true),
            ]),

            // Exchange
            'exchange_limit' => $this->when($request->has('exchange'), fn () => [
                'min' => currency()->getConvertedAmount(setting('exchange_minimum', 'exchange'), $currency, true),
                'max' => currency()->getConvertedAmount(setting('exchange_maximum', 'exchange'), $currency, true),
            ]),
        ];

        return self::collection($this->resource)->prepend(collect($defaultWallet)->filter(function ($value, $key) {
            return $value instanceof MissingValue ? false : true;
        })->toArray());
    }
}
