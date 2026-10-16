<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'icon'           => $this->icon,
            'sort_order'     => (int) ($this->sort_order ?? 0),
            'is_active'      => (bool) $this->is_active,
            'services_count' => $this->whenCounted('services', $this->services_count),
        ];
    }
}
