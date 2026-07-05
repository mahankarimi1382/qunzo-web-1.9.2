<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('type', 'site')->where('status', true)->first('name')?->name;
    }

    public function landingPages()
    {
        return $this->hasMany(LandingPage::class);
    }

    public function landingPageContents()
    {
        return $this->hasMany(LandingContent::class);
    }
}
