<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'content'        => $this->content,
            'excerpt'        => $this->excerpt(),
            'status'         => $this->status?->value ?? (string) $this->status,
            'is_solved'      => (bool) $this->is_solved,
            'visibility'     => $this->visibility,
            'group_id'       => $this->group_id,
            'user'           => new UserResource($this->whenLoaded('user')),
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'tags'           => TagResource::collection($this->whenLoaded('tags')),
            'media'          => PostMediaResource::collection($this->whenLoaded('media')),
            'group'          => $this->whenLoaded('group', fn () => new GroupResource($this->group)),
            'counts'         => [
                'views'    => (int) $this->views_count,
                'comments' => (int) $this->comments_count,
                'likes'    => (int) $this->likes_count,
                'shares'   => (int) $this->shares_count,
            ],
            'is_liked'      => $this->whenLoaded('likes', fn ($likes) => $likes->contains('user_id', auth()->id())),
            'is_saved'      => $this->whenLoaded('savedBy', fn ($savedBy) => $savedBy->contains('user_id', auth()->id())),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function excerpt(): string
    {
        return Str::limit(strip_tags($this->content), 200);
    }
}
