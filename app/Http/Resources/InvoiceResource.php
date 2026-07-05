<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'to' => $this->to,
            'email' => $this->email,
            'address' => $this->address,
            'currency' => $this->currency,
            'issue_date' => $this->issue_date,
            'items' => $this->items,
            'amount' => $this->amount,
            'charge' => $this->charge,
            'total_amount' => $this->total_amount,
            'is_paid' => $this->is_paid,
            'is_published' => $this->is_published,
            'is_crypto' => $this->wallet?->type === CurrencyType::Crypto,
            'transaction' => $this->when($this->transaction, function () {
                return [
                    'payment_gateway_url' => route('pay', ['transaction_id' => $this->transaction->tnx]),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
