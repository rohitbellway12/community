<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'event',
        'label',
        'description',
        'title',
        'body',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Resolve a template for the given event, substituting placeholders.
     * Falls back to defaults when no active template is found.
     */
    public static function resolve(
        string $event,
        string $defaultTitle,
        string $defaultMessage,
        array $variables = []
    ): array {
        $template = cache()->rememberForever("notification_template:{$event}", function () use ($event) {
            return NotificationTemplate::where('event', $event)
                ->where('is_active', true)
                ->first();
        });

        if (!$template) {
            return [
                $defaultTitle,
                static::interpolate($defaultMessage, $variables),
            ];
        }

        return [
            static::interpolate($template->title, $variables),
            static::interpolate($template->body, $variables),
        ];
    }

    public static function interpolate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace(':' . $key, (string) $value, $template);
        }

        return $template;
    }

    /**
     * Clear the cached template for this event.
     */
    public static function clearCache(string $event): void
    {
        cache()->forget("notification_template:{$event}");
        cache()->forget('notification_templates_all');
    }
}
