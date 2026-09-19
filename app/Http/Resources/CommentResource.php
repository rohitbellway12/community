<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;

        $avatar = $user?->profile?->avatar
            ? (str_starts_with($user->profile->avatar, 'http')
                ? $user->profile->avatar
                : asset('storage/' . ltrim($user->profile->avatar, '/')))
            : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';

        return [
            'id'                  => $this->id,
            'content'             => $this->content,
            'parent_id'           => $this->parent_id,
            'is_reply'            => (bool) $this->parent_id,
            'commented_at_human'  => $this->created_at?->diffForHumans(),
            'user'                => [
                'id'     => $user?->id,
                'name'   => $user?->name ?? 'User',
                'avatar' => $avatar,
            ],
            'replies'             => CommentResource::collection($this->whenLoaded('replies')),
            'likes_count'         => (int) $this->likes_count,
            'is_liked'            => $this->whenLoaded('likes', fn ($likes) => $likes->contains('user_id', auth()->id())),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
