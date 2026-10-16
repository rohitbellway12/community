<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'slug'                 => $this->slug,
            'service_category_id'  => $this->service_category_id,
            'category'             => new ServiceCategoryResource($this->whenLoaded('category')),
            'short_description'    => $this->short_description,
            'description'          => $this->description,
            'image'                => $this->image,
            'image_url'            => $this->image_url,
            'whatsapp_number'      => $this->whatsapp_number,
            'clean_whatsapp_number'=> $this->clean_whatsapp_number,
            'whatsapp_message'     => $this->whatsapp_message,
            'whatsapp_url'         => $this->whatsapp_url,
            'phone'                => $this->phone,
            'email'                => $this->email,
            'sort_order'           => (int) ($this->sort_order ?? 0),
            'status'               => $this->status ?? ($this->is_published ? 'active' : 'inactive'),
            'status_label'         => $this->status_label,
            'is_published'         => (bool) $this->is_published,
            'click_count'          => (int) ($this->click_count ?? 0),
            'created_at'           => $this->created_at?->toIso8601String(),
            'updated_at'           => $this->updated_at?->toIso8601String(),
        ];
    }
}
