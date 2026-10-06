<?php

namespace App\Http\Resources;

use App\Models\Follow;
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

        $currentUserId = $request->user('sanctum')?->id
            ?? $request->user()?->id
            ?? auth('sanctum')->id()
            ?? auth()->id();

        $isFollowing = false;
        $isSelf = false;

        if ($currentUserId) {
            $isSelf = (int) $currentUserId === (int) $this->id;
            if (!$isSelf) {
                $followingIds = $request->attributes->get('auth_following_ids');
                if ($followingIds === null) {
                    $followingIds = Follow::where('follower_id', $currentUserId)
                        ->pluck('following_id')
                        ->flip()
                        ->toArray();
                    $request->attributes->set('auth_following_ids', $followingIds);
                }
                $isFollowing = isset($followingIds[$this->id]);
            }
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'referral_code'  => $this->referral_code,
            'role'           => $this->role?->value ?? (string) $this->role,
            'status'         => $this->status?->value ?? (string) $this->status,
            'is_self'        => (bool) $isSelf,
            'is_following'   => (bool) $isFollowing,
            'email_verified' => !is_null($this->email_verified_at),
            'device'         => [
                'device_type' => $this->device_type,
                'device_os'   => $this->device_os,
                'browser'     => $this->browser,
                'ip_address'  => $this->ip_address,
                'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            ],
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
                'referrals' => $this->referred_count,
            ],
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
