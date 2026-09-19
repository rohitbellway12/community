<?php

namespace App\Traits;

use App\Models\NotificationTemplate;

trait ResolvesNotificationMessages
{
    /**
     * Resolve the notification title and message, using an admin-configurable
     * template when available and falling back to the hardcoded defaults.
     *
     * @return array{0: string, 1: string}  [title, message]
     */
    protected function resolveMessage(
        string $event,
        string $defaultTitle,
        string $defaultMessage,
        array $variables = []
    ): array {
        return NotificationTemplate::resolve($event, $defaultTitle, $defaultMessage, $variables);
    }
}
