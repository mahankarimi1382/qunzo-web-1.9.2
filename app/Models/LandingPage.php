<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeCurrentTheme($query)
    {
        return $query->where('theme', site_theme());
    }
}
