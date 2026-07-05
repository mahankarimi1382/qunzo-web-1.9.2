<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $guarded = ['id'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts()
    {
        return [
            'claimed_at' => 'datetime',
        ];
    }

    public function redeemer()
    {
        return $this->belongsTo(User::class, 'redeemer_id');
    }
}
