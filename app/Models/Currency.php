<?php

namespace App\Models;

use App\Enums\CurrencyStatus;
use App\Enums\CurrencyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'name',
        'code',
        'symbol',
        'conversion_rate',
        'status',
    ];

    public function wallets()
    {
        return $this->hasMany(UserWallet::class, 'currency_id');
    }

    protected function decimals(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->type === CurrencyType::Crypto ? 8 : 2;
        });
    }

    protected function casts()
    {
        return [
            'type' => CurrencyType::class,
            'status' => CurrencyStatus::class,
        ];
    }
}
