<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currency = $this->userWallet?->currency;

        return [
            'description' => $this->description,
            'tnx' => $this->tnx,
            'is_plus' => $this->user->role == UserType::User ? isPlusTransaction($this->type) : ($this->user->role == UserType::Agent ? isAgentPlusTransaction($this->type) : isMerchantPlusTransaction($this->type)),
            'type' => ucwords(str_replace('_', ' ', $this->type->value)),
            'amount' => formatAmount($this->amount, $currency, thousandSeparatorRemove: true),
            'charge' => formatAmount($this->charge, $currency, thousandSeparatorRemove: true),
            'final_amount' => formatAmount($this->final_amount, $this->pay_currency ?? setting('site_currency', 'global'), thousandSeparatorRemove: true),
            'status' => ucwords($this->status->value),
            'method' => $this->method,
            'created_at' => $this->created_at,
            'pay_currency' => $this->pay_currency ?? setting('site_currency', 'global'),
            'pay_amount' => $this->pay_amount ?? $this->amount,
            'wallet_type' => $this->wallet_type === 'default' ? 'Main Wallet' : $currency?->name ?? 'N/A',
            'is_crypto' => $this->wallet_type !== 'default' ? $currency?->type === CurrencyType::Crypto : false,
            'trx_currency' => $this->wallet_type === 'default' ? setting('site_currency', 'global') : $currency?->name ?? 'N/A',
            'trx_currency_symbol' => $this->wallet_type === 'default' ? setting('currency_symbol', 'global') : $currency?->symbol ?? 'N/A',
            'trx_currency_code' => $this->wallet_type === 'default' ? setting('site_currency', 'global') : $currency?->code ?? 'N/A',
        ];
    }
}
