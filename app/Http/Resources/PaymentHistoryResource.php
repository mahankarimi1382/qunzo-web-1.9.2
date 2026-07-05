<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tnx' => $this->tnx,
            'amount' => $this->amount,
            'charge' => $this->charge,
            'final_amount' => $this->final_amount,
            'currency' => $this->userWallet?->currency?->code ?? setting('site_currency', 'global'),
            'currency_symbol' => $this->userWallet?->currency?->symbol ?? setting('currency_symbol', 'global'),
            'status' => $this->status,
            'description' => $this->description,
            'method' => $this->method,
            'is_crypto' => $this->userWallet?->currency?->type === CurrencyType::Crypto,
            'wallet_name' => $this->userWallet?->currency?->name ?? 'Main Wallet',
            'merchant' => [
                'name' => $this->fromUser?->full_name,
                'account_number' => $this->fromUser?->account_number,
                'business_name' => $this->fromUser?->merchant?->business_name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
