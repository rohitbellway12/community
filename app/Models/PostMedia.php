<?php

// app/Models/PostMedia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'post_media';

    protected $fillable = [
        'post_id',
        'type',
        'file_path',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
