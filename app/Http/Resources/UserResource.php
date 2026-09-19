<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->profile;

        $avatarUrl = null;
        if ($profile && $profile->avatar) {
            $avatarUrl = str_starts_with($profile->avatar, 'http')
                ? $profile->avatar
                : asset('storage/' . $profile->avatar);
        } else {
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
        }

        $coverUrl = null;
        if ($profile && $profile->cover_image) {
            $coverUrl = str_starts_with($profile->cover_image, 'http')
                ? $profile->cover_image
                : asset('storage/' . $profile->cover_image);
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'role'           => $this->role?->value ?? (string) $this->role,
            'status'         => $this->status?->value ?? (string) $this->status,
            'email_verified' => !is_null($this->email_verified_at),
            'profile'        => [
                'id'          => $profile?->id,
                'username'    => $profile?->username,
                'bio'         => $profile?->bio,
                'location'    => $profile?->location,
                'avatar'      => $avatarUrl,
                'cover_image' => $coverUrl,
                'country'     => $profile?->country ? new CountryResource($profile->country) : null,
                'joined_at'   => $profile?->joined_at?->toIso8601String(),
            ],
            'counts' => [
                'posts'     => $this->whenCounted('posts', $this->posts_count, fn() => $this->posts()->count()),
                'followers' => $this->whenCounted('followers', $this->followers_count, fn() => $this->followers()->count()),
                'following' => $this->whenCounted('following', $this->following_count, fn() => $this->following()->count()),
            ],
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
