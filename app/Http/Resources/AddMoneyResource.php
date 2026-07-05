<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddMoneyResource extends JsonResource
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
            'name' => $this->name,
            'formatted_name' => $this->name.($this->type != 'auto' ? ' (Manual)' : ''),
            'gateway_code' => $this->gateway_code,
            'type' => $this->type,
            'currency' => $this->currency,
            'symbol' => $this->symbol,
            'currency_decimals' => $this->currency_decimals,
            'minimum_deposit' => $this->minimum_deposit,
            'maximum_deposit' => $this->maximum_deposit,
            'instructions' => filled($this->payment_details) ? $this->payment_details : null,
            'charge' => $this->charge,
            'charge_type' => $this->charge_type,
            'currency_type' => $this->whenLoaded('currencyData', function () {
                $currencyData = $this?->currencyData;

                return $currencyData->type ?? 'flat';
            }, 'flat') ?? 'flat',
            'conversion_rate' => $this->conversion_rate,
            'status' => $this->status,
            'image' => $this->image ? asset($this->image) : null,
            'field_options' => $this->when($this->type === 'manual', $this->field_options),
            'field_name' => $this->when($this->type === 'manual', $this->field_name),
            'details' => $this->details,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
