<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'rules',
        'banner_image',
        'banner_link_url',
        'start_at',
        'end_at',
        'posts_weight',
        'comments_weight',
        'likes_weight',
        'referrals_weight',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_at'          => 'datetime',
        'end_at'            => 'datetime',
        'posts_weight'      => 'integer',
        'comments_weight'   => 'integer',
        'likes_weight'      => 'integer',
        'referrals_weight'  => 'integer',
        'sort_order'        => 'integer',
    ];

    /**
     * Scope: only events currently active by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: event is currently running (within start/end time window).
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    /**
     * Check if this event is currently within its time window.
     */
    public function isRunning(): bool
    {
        return $this->status === 'active'
            && $this->start_at->lte(now())
            && $this->end_at->gte(now());
    }

    /**
     * Get the event's banner image URL.
     */
    public function getBannerImageUrlAttribute(): ?string
    {
        if (empty($this->banner_image)) {
            return null;
        }

        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }

        return asset('storage/' . ltrim($this->banner_image, '/'));
    }

    /**
     * Auto-generate slug from title if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title) . '-' . Str::random(5);
            }
        });
    }

    /**
     * Calculate the top N users for this event based on activity during event period.
     * Counts: posts, comments, likes received, referrals — each weighted.
     */
    public function getTopUsers(int $limit = 5): \Illuminate\Support\Collection
    {
        $startAt         = $this->start_at;
        $endAt           = $this->end_at;
        $postsW          = (float) $this->posts_weight;
        $commentsW       = (float) $this->comments_weight;
        $likesW          = (float) $this->likes_weight;
        $referralsW      = (float) $this->referrals_weight;

        return User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.referral_code',
            ])
            ->with(['profile:id,user_id,username,avatar'])
            ->selectRaw("
                (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id AND posts.deleted_at IS NULL AND posts.status = 'published' AND posts.created_at BETWEEN ? AND ?) AS posts_count,
                (SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.deleted_at IS NULL AND comments.created_at BETWEEN ? AND ?) AS comments_count,
                (SELECT COUNT(*) FROM likes INNER JOIN posts ON posts.id = likes.post_id WHERE posts.user_id = users.id AND posts.deleted_at IS NULL AND likes.created_at BETWEEN ? AND ?) AS likes_count,
                (SELECT COUNT(*) FROM referrals WHERE referrals.referrer_id = users.id AND referrals.created_at BETWEEN ? AND ?) AS referrals_count
            ", [
                $startAt, $endAt,
                $startAt, $endAt,
                $startAt, $endAt,
                $startAt, $endAt,
            ])
            ->selectRaw("
                (
                    COALESCE((SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id AND posts.deleted_at IS NULL AND posts.status = 'published' AND posts.created_at BETWEEN ? AND ?), 0) * ?
                    +
                    COALESCE((SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.deleted_at IS NULL AND comments.created_at BETWEEN ? AND ?), 0) * ?
                    +
                    COALESCE((SELECT COUNT(*) FROM likes INNER JOIN posts ON posts.id = likes.post_id WHERE posts.user_id = users.id AND posts.deleted_at IS NULL AND likes.created_at BETWEEN ? AND ?), 0) * ?
                    +
                    COALESCE((SELECT COUNT(*) FROM referrals WHERE referrals.referrer_id = users.id AND referrals.created_at BETWEEN ? AND ?), 0) * ?
                ) AS event_score
            ", [
                $startAt, $endAt, $postsW,
                $startAt, $endAt, $commentsW,
                $startAt, $endAt, $likesW,
                $startAt, $endAt, $referralsW,
            ])
            ->having('event_score', '>', 0)
            ->orderByDesc('event_score')
            ->limit($limit)
            ->get()
            ->map(function ($u) {
                $u->total_score = (int) $u->event_score;
                $profile = $u->profile;
                $u->avatar = $profile?->avatar
                    ? (str_starts_with($profile->avatar, 'http') ? $profile->avatar : asset('storage/' . $profile->avatar))
                    : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0D8ABC&color=fff';
                return $u;
            });
    }

    /**
     * Get a single user's score and rank for this event.
     */
    public function getUserScore(User $user): array
    {
        $startAt    = $this->start_at;
        $endAt      = $this->end_at;

        $posts = \DB::table('posts')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->whereBetween('created_at', [$startAt, $endAt])
            ->count();

        $comments = \DB::table('comments')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$startAt, $endAt])
            ->count();

        $likes = \DB::table('likes')
            ->join('posts', 'posts.id', '=', 'likes.post_id')
            ->where('posts.user_id', $user->id)
            ->whereNull('posts.deleted_at')
            ->whereBetween('likes.created_at', [$startAt, $endAt])
            ->count();

        $referrals = \DB::table('referrals')
            ->where('referrer_id', $user->id)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->count();

        $score = ($posts * $this->posts_weight)
            + ($comments * $this->comments_weight)
            + ($likes * $this->likes_weight)
            + ($referrals * $this->referrals_weight);

        return compact('posts', 'comments', 'likes', 'referrals', 'score');
    }
}
