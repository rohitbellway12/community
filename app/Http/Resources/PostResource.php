<?php

namespace App\Http\Resources;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user('sanctum')?->id
            ?? $request->user()?->id
            ?? auth('sanctum')->id()
            ?? auth()->id();

        $isLiked = false;
        if ($currentUserId) {
            $isLiked = $this->relationLoaded('likes')
                ? $this->likes->contains('user_id', $currentUserId)
                : $this->likes()->where('user_id', $currentUserId)->exists();
        }

        $isSaved = false;
        if ($currentUserId) {
            $isSaved = $this->relationLoaded('savedBy')
                ? $this->savedBy->contains('user_id', $currentUserId)
                : $this->savedBy()->where('user_id', $currentUserId)->exists();
        }

        $isFollowing = false;
        if ($currentUserId && $this->user_id) {
            if ((int) $currentUserId !== (int) $this->user_id) {
                $followingIds = $request->attributes->get('auth_following_ids');
                if ($followingIds === null) {
                    $followingIds = Follow::where('follower_id', $currentUserId)
                        ->pluck('following_id')
                        ->flip()
                        ->toArray();
                    $request->attributes->set('auth_following_ids', $followingIds);
                }
                $isFollowing = isset($followingIds[$this->user_id]);
            }
        }

        $shareUrl = route('community.posts.show', $this->id);
        $firstMedia = $this->relationLoaded('media') ? $this->media->first() : null;
        $shareMediaUrl = $firstMedia ? asset('storage/' . $firstMedia->file_path) : null;
        $shareMediaType = ($firstMedia && (($firstMedia->type ?? '') === 'video' || str_starts_with($firstMedia->mime_type ?? '', 'video') || preg_match('/\.(mp4|mov|avi|webm|mkv|m4v|qt|3gp|ogg|wmv)$/i', $firstMedia->file_path)))
            ? 'video'
            : ($firstMedia ? 'image' : null);

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
            'is_liked'      => (bool) $isLiked,
            'is_saved'      => (bool) $isSaved,
            'is_following'  => (bool) $isFollowing,
            'share_url'     => $shareUrl,
            'share_text'    => $this->getShareText(),
            'share_data'    => [
                'title'       => $this->title,
                'text'        => $this->getShareText(),
                'url'         => $shareUrl,
                'media_url'   => $shareMediaUrl,
                'media_type'  => $shareMediaType,
                'endpoint'    => route('api.v1.posts.share', $this->id),
            ],
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function excerpt(): string
    {
        return Str::limit(strip_tags($this->content), 200);
    }

    protected function getShareText(): string
    {
        $url = route('community.posts.show', $this->id);
        $excerpt = Str::limit(strip_tags($this->content ?? ''), 160);
        $authorName = $this->relationLoaded('user') && $this->user ? $this->user->name : null;
        $byAuthor = $authorName ? " by {$authorName}" : '';
        return trim("{$this->title}{$byAuthor}\n\n\"{$excerpt}\"\n\n👉 Read full post: {$url}");
    }
}
