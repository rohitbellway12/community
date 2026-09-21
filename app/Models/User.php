<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_seen_at',
        'referral_code',
        'device_type',
        'device_os',
        'browser',
        'ip_address',
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'last_seen_at' => 'datetime',
            'ip_address' => 'string',
        ];
    }

    /**
     * User profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * User posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * User comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * User likes.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * User shares.
     */
    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    /**
     * Saved posts.
     */
    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

public function followers()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'following_id',
        'follower_id'
    )->withTimestamps();
}

public function following()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'follower_id',
        'following_id'
    )->withTimestamps();
}

    /**
     * User statistics.
     */
    public function stats()
    {
        return $this->hasOne(UserStat::class);
    }

    /**
     * User activity logs.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Reports created by this user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Admin audit logs created by this user.
     */
    public function adminAuditLogs()
    {
        return $this->hasMany(AdminAuditLog::class, 'admin_id');
    }


    public function groups(): BelongsToMany
{
    return $this->belongsToMany(Group::class, 'group_user')
        ->withPivot(['role', 'status'])
        ->withTimestamps();
}

public function ownedGroups(): HasMany
{
    return $this->hasMany(Group::class, 'owner_id');
}

public function activeGroups(): BelongsToMany
{
    return $this->groups()->wherePivot('status', 'active');
}

    /**
     * Get top contributors based on activity points:
     * (posts * 5) + (comments * 3) + (likes_received * 2) + (shares_received * 2)
     */
    public static function getTopContributors(int $limit = 5)
    {
        return static::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->with([
                'profile:id,user_id,username,avatar',
            ])
            ->withCount([
                'posts' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('status', 'published')
                        ->where('visibility', 'public');
                },
                'comments' => function ($query) {
                    $query->whereNull('deleted_at');
                },
            ])
            ->selectRaw('
                (
                    COALESCE((
                        SELECT COUNT(*)
                        FROM posts
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                        AND posts.status = "published"
                        AND posts.visibility = "public"
                    ), 0) * 5
                    +
                    COALESCE((
                        SELECT COUNT(*)
                        FROM comments
                        WHERE comments.user_id = users.id
                        AND comments.deleted_at IS NULL
                    ), 0) * 3
                    +
                    COALESCE((
                        SELECT COUNT(*)
                        FROM likes
                        INNER JOIN posts ON posts.id = likes.post_id
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                        AND posts.status = "published"
                        AND posts.visibility = "public"
                    ), 0) * 2
                    +
                    COALESCE((
                        SELECT COUNT(*)
                        FROM shares
                        INNER JOIN posts ON posts.id = shares.post_id
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                        AND posts.status = "published"
                        AND posts.visibility = "public"
                    ), 0) * 2
                ) AS contributor_points
            ')
            ->where(function ($query) {
                $query->whereHas('posts', function ($q) {
                    $q->whereNull('deleted_at')
                        ->where('status', 'published')
                        ->where('visibility', 'public');
                })->orWhereHas('comments', function ($q) {
                    $q->whereNull('deleted_at');
                });
            })
            ->orderByDesc('contributor_points')
            ->orderByDesc('posts_count')
            ->orderByDesc('comments_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Determine if the user is currently online (visited within specified minutes).
     */
    public function isOnline(int $minutes = 5): bool
    {
        return $this->last_seen_at !== null && $this->last_seen_at->gte(now()->subMinutes($minutes));
    }

    /**
     * Get a human-readable online status description.
     */
    public function onlineStatusText(int $minutes = 5): string
    {
        if ($this->isOnline($minutes)) {
            return 'Online';
        }

        if ($this->last_seen_at) {
            return 'Offline • Last seen ' . $this->last_seen_at->diffForHumans();
        }

        return 'Offline • Never seen';
    }

    /**
     * Get device type label with icon.
     */
    public function deviceTypeIcon(): string
    {
        return match ($this->device_type) {
            'Mobile' => '📱',
            'Tablet' => '📋',
            default => '💻',
        };
    }

    /**
     * Get formatted device info string.
     */
    public function deviceInfo(): string
    {
        $parts = [];
        if ($this->device_os) {
            $parts[] = $this->device_os;
        }
        if ($this->browser) {
            $parts[] = '(' . $this->browser . ')';
        }
        return implode(' ', $parts);
    }

    /**
     * Get device type label.
     */
    public function deviceTypeLabel(): string
    {
        return $this->device_type ?? 'Desktop';
    }

    /**
     * Get device icon HTML.
     */
    public function deviceIcon(): string
    {
        return match ($this->device_type) {
            'Mobile' => '📱',
            'Tablet' => '📋',
            default => '💻',
        };
    }

    /**
     * Daily visits relationship.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(UserVisit::class);
    }

    /**
     * Referrals this user gave (users who joined using this user's code).
     */
    public function referralsGiven(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Referral record for this user (the code they used when signing up).
     */
    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Count of users this user has successfully referred.
     */
    public function getReferredCountAttribute(): int
    {
        return $this->referralsGiven()->count();
    }
}