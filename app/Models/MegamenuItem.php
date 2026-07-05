<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegamenuItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the translated title.
     */
    protected function ttitle(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);

                return $jsonData[session()->get('locale') ?? config('app.locale')]['title'] ?? $this->title;
            }

            return $this->title;
        });
    }

    /**
     * Get the translated description.
     */
    protected function tdescription(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);

                return $jsonData[session()->get('locale') ?? config('app.locale')]['description'] ?? $this->description;
            }

            return $this->description;
        });
    }

    /**
     * Get the translated preview title.
     */
    protected function tpreviewTitle(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);

                return $jsonData[session()->get('locale') ?? config('app.locale')]['preview_title'] ?? $this->preview_title;
            }

            return $this->preview_title;
        });
    }

    /**
     * Get the translated preview description.
     */
    protected function tpreviewDescription(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->translate != null) {
                $jsonData = json_decode($this->translate, true);

                return $jsonData[session()->get('locale') ?? config('app.locale')]['preview_description'] ?? $this->preview_description;
            }

            return $this->preview_description;
        });
    }

    /**
     * Get the menu URL.
     */
    protected function menuUrl(): Attribute
    {
        return Attribute::make(get: function () {

            if ($this->page_id && $this->page) {
                return $this->page->url;
            }

            return $this->url;
        });
    }
}
