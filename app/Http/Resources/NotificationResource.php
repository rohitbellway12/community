<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->data;

        return [
            'id'           => $this->id,
            'type'         => $data['type'] ?? null,
            'title'        => $data['title'] ?? null,
            'message'      => $data['message'] ?? null,
            'is_read'      => $this->read_at !== null,
            'created_at'   => $this->created_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'data'         => $data,
        ];
    }
}
