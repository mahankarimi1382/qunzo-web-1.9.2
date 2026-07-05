<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tnx' => $this->tnx,
            'amount' => $this->amount,
            'charge' => $this->charge,
            'final_amount' => $this->final_amount,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'method' => $this->method,
            'pay_currency' => $this->pay_currency,
            'pay_amount' => $this->pay_amount,
            'receive_currency' => $this->userWallet?->currency?->code ?? setting('site_currency', 'global'),
            'receive_currency_symbol' => $this->userWallet?->currency?->symbol ?? setting('currency_symbol', 'global'),
            'wallet_name' => $this->userWallet?->currency?->name ?? 'Main Wallet',
            'is_crypto' => $this->userWallet?->currency?->type === CurrencyType::Crypto,
            'exchange_rate' => $this->pay_amount > 0 ? round($this->amount / $this->pay_amount, 4) : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
