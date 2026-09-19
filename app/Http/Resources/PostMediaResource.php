<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'url'        => $this->file_path
                ? asset('storage/' . ltrim($this->file_path, '/'))
                : null,
            'mime_type'  => $this->mime_type,
            'file_size'  => $this->file_size,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
