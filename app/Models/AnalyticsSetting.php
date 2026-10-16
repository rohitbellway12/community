<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSetting extends Model
{
    protected $fillable = [
        'google_analytics_id',
        'is_enabled',
        'play_store_url',
        'app_store_url',
        'custom_head_scripts',
        'custom_body_scripts',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function config(): self
    {
        return cache()->rememberForever('analytics_settings_config', function () {
            return static::firstOrCreate([], [
                'google_analytics_id' => null,
                'is_enabled'          => false,
                'play_store_url'      => null,
                'app_store_url'       => null,
                'custom_head_scripts' => null,
                'custom_body_scripts' => null,
            ]);
        });
    }

    public static function clearCache(): void
    {
        cache()->forget('analytics_settings_config');
    }
}
