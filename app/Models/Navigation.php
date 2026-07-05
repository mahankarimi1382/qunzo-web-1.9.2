<?php

namespace App\Models;

use App\Enums\MegamenuType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => 'json',
            'megamenu_type' => MegamenuType::class,
        ];
    }

    public function page()
    {
        return $this->belongsTo(Page::class)->withDefault();
    }

    public function megamenuItems()
    {
        return $this->hasMany(MegamenuItem::class)->orderBy('sort_order');
    }

    public function activeMegamenuItems()
    {
        return $this->hasMany(MegamenuItem::class)
            ->where('status', 1)
            ->orderBy('sort_order');
    }

    protected function tname(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);
                $localeData = $jsonData[app()->getLocale()] ?? null;
                if (is_array($localeData)) {
                    return $localeData['name'] ?? $this->name;
                }

                return $localeData ?? $this->name;
            }

            return $this->name;
        });
    }

    protected function tmegamenuName(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);
                $localeData = $jsonData[app()->getLocale()] ?? null;
                if (is_array($localeData)) {
                    return $localeData['megamenu_name'] ?? ($this->megamenu_name ?? $this->name);
                }
            }

            return $this->megamenu_name ?? $this->name;
        });
    }

    public function getTypeNameAttribute()
    {
        return collect($this->type)->map(function ($type) {
            return ucwords(str($type)->replace('_', ' '));
        })->implode(',');
    }
}
