<?php

namespace App\Models;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SandboxTransaction extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => TxnType::class,
            'status' => TxnStatus::class,
            'amount' => 'double',
            'manual_field_data' => 'json',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaction) {
            $transaction->tnx = 'TRX'.strtoupper(Str::random(10));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
