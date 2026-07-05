<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function method()
    {
        return $this->belongsTo(WithdrawMethod::class, 'withdraw_method_id');
    }

    public function wallet()
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id');
    }
}
