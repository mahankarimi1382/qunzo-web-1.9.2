<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shortDetails = Str::of($this->details)
            ->pipe(fn ($v) => html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            ->stripTags()
            ->replaceMatches('/\R+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim();

        return [
            'id' => $this->id,
            'page_title' => $this->title.' | '.setting('site_title', 'global'),
            'title' => $this->title,
            'short_details' => trim($shortDetails),
            'details' => $this->details,
            'cover' => asset($this->cover),
            'created_at' => $this->created_at,
        ];
    }
}
