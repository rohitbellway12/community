<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
}