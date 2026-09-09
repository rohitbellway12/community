<?php

// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'bio',
        'avatar',
        'cover_image',
        'country_id',
        'location',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
