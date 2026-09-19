<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'description',
        'is_push_enabled',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_push_enabled' => 'boolean',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public static function isPushEnabled(string $key): bool
    {
        $settings = static::allCached();

        return (bool) ($settings[$key] ?? false);
    }

    /**
     * Cache all notification settings (key => is_push_enabled) for fast lookup.
     */
    public static function allCached(): array
    {
        return cache()->rememberForever('notification_settings_all', function () {
            return static::where('is_active', true)
                ->pluck('is_push_enabled', 'key')
                ->toArray();
        });
    }

    /**
     * Clear the cached settings.
     */
    public static function clearCache(): void
    {
        cache()->forget('notification_settings_all');

        foreach (static::pluck('key')->all() as $key) {
            cache()->forget("notification_setting:{$key}");
        }
    }
}
