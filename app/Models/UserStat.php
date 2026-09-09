<?php

// app/Models/UserStat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory;

    protected $table = 'user_stats';

    protected $fillable = [
        'user_id',
        'posts_count',
        'comments_count',
        'likes_received_count',
        'shares_count',
        'followers_count',
        'following_count',
    ];

    protected $casts = [
        'posts_count' => 'integer',
        'comments_count' => 'integer',
        'likes_received_count' => 'integer',
        'shares_count' => 'integer',
        'followers_count' => 'integer',
        'following_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
