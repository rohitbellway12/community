<?php

// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
