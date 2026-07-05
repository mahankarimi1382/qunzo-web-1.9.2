<?php

namespace App\Models;

use App\Enums\RequestMoneyStatus;
use Illuminate\Database\Eloquent\Model;

class MoneyRequest extends Model
{
    protected $guarded = ['id'];

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function currencyData()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code')->withDefault([
            'name' => setting('site_currency', 'global'),
            'code' => setting('site_currency', 'global'),
            'symbol' => setting('currency_symbol', 'global'),
        ]);
    }

    public function recipientWallet()
    {
        return $this->hasOne(UserWallet::class, 'user_id', 'recipient_user_id')->with('currency')->where('currency_id', $this->currencyData->id)->withDefault([
            'balance' => $this->recipient?->balance ?? 0,
            'name' => 'Main Wallet',
            'id' => 0,
            'currency' => [
                'name' => setting('site_currency', 'global'),
                'code' => setting('site_currency', 'global'),
                'symbol' => setting('currency_symbol', 'global'),
            ],
        ]);
    }

    public function requesterWallet()
    {
        return $this->hasOne(UserWallet::class, 'user_id', 'requester_user_id')->with('currency')->where('currency_id', $this->currencyData->id)->withDefault([
            'balance' => $this->recipient?->balance ?? 0,
            'name' => 'Main Wallet',
            'id' => 0,
            'currency' => [
                'name' => setting('site_currency', 'global'),
                'code' => setting('site_currency', 'global'),
                'symbol' => setting('currency_symbol', 'global'),
            ],
        ]);
    }

    protected function casts()
    {
        return [
            'status' => RequestMoneyStatus::class,
        ];
    }
}
