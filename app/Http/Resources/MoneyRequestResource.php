<?php

namespace App\Http\Resources;

use App\Enums\RequestMoneyStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyRequestResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth()->user();
        $isRequester = $this->requester_user_id === $user->id;

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'charge' => $this->charge,
            'final_amount' => $this->final_amount,
            'currency' => $this->currencyData?->code ?? setting('site_currency', 'global'),
            'currency_symbol' => $this->currencyData?->symbol ?? setting('currency_symbol', 'global'),
            'requester_wallet_currency_name' => $this->requesterWallet?->currency?->name ?? 'Main Wallet',
            'recipient_wallet_currency_name' => $this->recipientWallet?->currency?->name ?? 'Main Wallet',
            'status' => $this->status,
            'note' => $this->note,
            'type' => $isRequester ? 'sent' : 'received',
            'is_crypto' => $this->currencyData?->type?->value == 'crypto',
            'requester' => $this->whenLoaded('requester', fn () => [
                'id' => $this->requester->id,
                'name' => $this->requester->full_name,
                'email' => $this->requester->email,
                'account_number' => $this->requester->account_number,
                'avatar' => $this->requester->avatar,
            ]),
            'recipient' => $this->whenLoaded('recipient', fn () => [
                'id' => $this->recipient->id,
                'name' => $this->recipient->full_name,
                'email' => $this->recipient->email,
                'account_number' => $this->recipient->account_number,
                'avatar' => $this->recipient->avatar,
            ]),
            'can_action' => ! $isRequester && $this->status == RequestMoneyStatus::Pending,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_crypto' => $this->currencyData?->type?->value == 'crypto',

        ];
    }
}
