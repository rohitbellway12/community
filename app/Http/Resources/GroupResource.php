<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,
            'description'        => $this->description,
            'visibility'         => $this->visibility ?? 'public',
            'cover_image'        => $this->cover_image
                ? asset('storage/' . ltrim($this->cover_image, ''))
                : null,
            'owner'              => new UserResource($this->whenLoaded('owner')),
            'members_count'      => $this->whenCounted('users'),
            'posts_count'        => $this->whenCounted('posts'),
            'is_owner'           => (bool) ($this->is_owner ?? false),
            'is_member'          => (bool) ($this->is_member ?? false),
            'membership_role'    => $this->membership_role ?? null,
            'membership_status'  => $this->membership_status ?? null,
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
