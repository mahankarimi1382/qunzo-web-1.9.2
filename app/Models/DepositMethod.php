<?php

namespace App\Models;

use App\Enums\CurrencyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositMethod extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'gateway_logo',
        'currency_decimals',
    ];

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function scopeCode($query, $code)
    {
        return $query->where('gateway_code', $code);
    }

    protected function gatewayLogo(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->logo == null) {
                return asset($this->gateway?->logo);
            }

            return asset($this->logo);
        });
    }

    protected function currencyDecimals(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->currencyData?->type == CurrencyType::Crypto ? 8 : 2;
        });
    }

    public function currencyData()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    protected function casts(): array
    {
        return [
            'field_options' => 'json',
        ];
    }
}
