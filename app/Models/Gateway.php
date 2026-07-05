<?php

namespace App\Models;

use App\Enums\GatewayType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeCode($query, $code)
    {
        return $query->where('gateway_code', $code);
    }

    protected function casts(): array
    {
        return [
            'type' => GatewayType::class,
        ];
    }
}
