<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth()->user();
        $isCreator = $this->user_id === $user->id;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'amount' => $this->amount,
            'charge' => $this->charge,
            'final_amount' => $this->final_amount,
            'currency' => $this->currency ? $this->currency->code : setting('site_currency', 'global'),
            'currency_symbol' => $this->currency ? $this->currency->symbol : setting('currency_symbol', 'global'),
            'is_redeemed' => ! is_null($this->redeemer_id),
            'type' => $isCreator ? 'created' : 'redeemed',
            'is_crypto' => $this->currency ? $this->currency->type === CurrencyType::Crypto : false,
            'creator' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
                'email' => $this->user->email,
                'account_number' => $this->user->account_number,
                'avatar' => $this->user->avatar,
            ],
            'redeemer' => $this->when($this->redeemer, function () {
                return [
                    'id' => $this->redeemer->id,
                    'name' => $this->redeemer->full_name,
                    'email' => $this->redeemer->email,
                    'account_number' => $this->redeemer->account_number,
                    'avatar' => $this->redeemer->avatar,
                ];
            }),
            'created_at' => $this->created_at,
            'claimed_at' => $this->claimed_at,
        ];
    }
}
