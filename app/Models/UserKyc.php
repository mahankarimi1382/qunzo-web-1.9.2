<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserKyc extends Model
{
    use HasApiTokens, HasFactory;

    protected $guarded = [];

    public function kyc()
    {
        return $this->hasOne(Kyc::class, 'id', 'kyc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
