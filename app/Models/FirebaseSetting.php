<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseSetting extends Model
{
    protected $fillable = [
        'project_id',
        'credentials_json',
        'server_key',
        'is_enabled',
        'default_title',
        'default_icon',
        'default_color',
    ];

    protected $casts = [
        'is_enabled'      => 'boolean',
        'credentials_json'=> 'encrypted',
    ];

    public static function config(): self
    {
        return cache()->rememberForever('firebase_settings_config', function () {
            return static::firstOrCreate([], [
                'default_title'  => config('fcm.default_title', 'Community App'),
                'default_color'  => config('fcm.default_color', '#0D8ABC'),
                'is_enabled'     => config('fcm.server_key') ? true : false,
            ]);
        });
    }

    public static function clearCache(): void
    {
        cache()->forget('firebase_settings_config');
        cache()->forget('push_notification_config');
    }
}
