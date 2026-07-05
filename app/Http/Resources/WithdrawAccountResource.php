<?php

namespace App\Http\Resources;

use App\Enums\CurrencyType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawAccountResource extends JsonResource
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
            'method_name' => $this->method_name,
            'currency' => $this->method->currency,
            'wallet_name' => $this->method->currency == setting('site_currency', 'global') ? 'Main Wallet' : $this->wallet?->currency?->name,
            'method' => [
                'id' => $this->method->id,
                'name' => $this->method->name,
                'icon' => $this->getLogo(),
                'type' => $this->method->type,
                'min_withdraw' => formatAmount($this->method->min_withdraw, $this->method->currency, false, true),
                'max_withdraw' => formatAmount($this->method->max_withdraw, $this->method->currency, false, true),
                'is_crypto' => $this->wallet?->currency?->type === CurrencyType::Crypto,
                'charge' => $this->method->charge,
                'rate' => (float) $this->method->rate,
                'charge_type' => $this->method->charge_type,
                'time' => $this->method->required_time.' '.$this->method->required_time_format,
                'fields' => $this->getFields(),
            ],
            'created_at' => $this->created_at,
        ];
    }

    public function getFields()
    {
        $fields = json_decode($this->credentials, true) ?? [];

        return collect($fields)->map(function ($field, $name) {
            if (isset($field['value']) && is_string($field['value']) && file_exists(base_path('public/'.$field['value']))) {
                $field['value'] = asset($field['value']);
            }

            return array_merge($field, ['name' => $name]);
        })->values()->toArray();
    }

    public function getLogo()
    {
        $icon = $this->method->icon;
        if ($this->method->gateway_id != null && $this->method->icon == '') {
            $icon = $this->method->gateway->logo;
        }

        return asset($icon);
    }
}
