<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts()
    {
        return [
            'is_default' => 'boolean',
            'is_rtl' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function scopeOrder($query, string $order)
    {
        return $query->orderBy('id', $order);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('locale', 'like', '%'.$search.'%');
        }

        return $query;
    }
}
