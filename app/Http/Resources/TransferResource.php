<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'currency' => $this->userWallet?->currency?->code ?? setting('site_currency', 'global'),
            'currency_symbol' => $this->userWallet?->currency?->symbol ?? setting('currency_symbol', 'global'),
            'wallet_name' => $this->userWallet?->currency?->name ?? 'Main Wallet',
            'sender' => $this->whenLoaded('fromUser'),
            'recipient' => $this->whenLoaded('user'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_crypto' => $this->userWallet?->currency?->type === CurrencyType::Crypto,
            'approval_cause' => $this->approval_cause,
        ];
    }
}
