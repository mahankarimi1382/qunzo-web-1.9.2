<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: function () {
            return Carbon::parse($this->attributes['created_at'])->format('M d Y h:i');
        });
    }

    public function scopeOrder($query, string $order)
    {
        return $query->orderBy('id', $order);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('email', 'like', '%'.$search.'%');
        }

        return $query;
    }
}
