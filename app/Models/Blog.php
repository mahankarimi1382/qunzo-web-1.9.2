<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: function () {
            return Carbon::parse($this->attributes['created_at'])->format('d F Y');
        });
    }

    protected function unModifyCreatedAt(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['created_at'];
        });
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('details', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }
}
