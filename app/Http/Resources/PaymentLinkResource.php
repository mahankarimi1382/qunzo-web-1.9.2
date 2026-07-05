<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentLinkResource extends JsonResource
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
            'number' => $this->number,
            'notes' => $this->address,
            'is_paid' => $this->is_paid,
            'payment_link' => route('pay', ['transaction_id' => $this->transaction->tnx]),
            'created_at' => $this->created_at->format('d M Y h:i A'),
        ];
    }
}
