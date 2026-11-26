<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesSetting extends Model
{
    protected $table = 'services_settings';

    protected $fillable = [
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function isServicesEnabled(): bool
    {
        return cache()->rememberForever('services_global_enabled', function () {
            return (bool) (static::first()?->is_enabled ?? true);
        });
    }

    public static function toggle(): bool
    {
        $setting = static::firstOrCreate([], ['is_enabled' => true]);
        $setting->is_enabled = !$setting->is_enabled;
        $setting->save();

        cache()->forget('services_global_enabled');

        return $setting->is_enabled;
    }

    public static function setEnabled(bool $enabled): void
    {
        $setting = static::firstOrCreate([], ['is_enabled' => true]);
        $setting->is_enabled = $enabled;
        $setting->save();

        cache()->forget('services_global_enabled');
    }
}
