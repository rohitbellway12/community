<?php

// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    use HasFactory;

    /**
     * Generate a short, unique username in Instagram style (e.g. rohit482).
     */
    public static function generateUniqueUsername(string $name): string
    {
        $firstName = explode(' ', trim($name))[0] ?? '';
        $baseName = Str::slug($firstName);

        if (empty($baseName) || strlen($baseName) < 2) {
            $baseName = 'user';
        }

        // Limit base name to max 10 characters so total username stays under 13-14 chars
        $baseName = substr($baseName, 0, 10);

        do {
            $username = $baseName . rand(100, 999);
        } while (static::where('username', $username)->exists());

        return $username;
    }

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
