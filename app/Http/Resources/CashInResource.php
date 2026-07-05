<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class CashInResource extends JsonResource
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
            'is_crypto' => $this->userWallet?->currency?->type === CurrencyType::Crypto,
            'user' => $this->when($this->user, [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
                'email' => $this->user->email,
                'account_number' => $this->user->account_number,
                'avatar' => $this->user->avatar_path,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
