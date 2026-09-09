<?php

// app/Models/Post.php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'status',
        'is_solved',
        'views_count',
        'visibility',
        'comments_count',
        'likes_count',
        'shares_count',
        'group_id',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'is_solved' => 'boolean',
        'views_count' => 'integer',
        'comments_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    public function savedBy()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
