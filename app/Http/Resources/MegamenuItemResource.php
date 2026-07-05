<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MegamenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->ttitle,
            'description' => $this->tdescription,
            'icon' => $this->icon ? asset($this->icon) : null,
            'url' => $this->menuUrl,
            'preview_title' => $this->tpreviewTitle,
            'preview_description' => $this->tpreviewDescription,
            'preview_image' => $this->preview_image ? asset($this->preview_image) : null,
            'is_featured' => (bool) $this->is_featured,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
