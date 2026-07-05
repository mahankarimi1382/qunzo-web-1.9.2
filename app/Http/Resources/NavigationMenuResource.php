<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->tname,
            'megamenu_name' => $this->tmegamenu_name,
            'slug' => $this->page_id == null ? $this->url : $this->page?->url,
            'url' => $this->url,
            'has_megamenu' => (bool) $this->has_megamenu,
            'megamenu_type' => (int) $this->megamenu_type?->value,
            'megamenu_items' => MegamenuItemResource::collection($this->activeMegamenuItems),
        ];
    }
}
