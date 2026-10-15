<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data ?? '[]', true);

        // Resolve notification type (supports admin_broadcast and standard notification types)
        $type = $data['type'] ?? ($data['notification_type'] ?? null);
        if (!$type && str_contains($this->type ?? '', 'AdminBroadcast')) {
            $type = 'admin_broadcast';
        }

        // Resolve message / body text
        $message = $data['message'] ?? ($data['body'] ?? null);

        // Resolve action & redirection details for mobile app navigation
        $actionLink = $data['action_link'] ?? '';
        $targetType = $data['target_type'] ?? 'home';
        $targetId = $data['target_id'] ?? null;
        $redirectUrl = $data['url'] ?? ($data['redirect_url'] ?? null);

        if (!empty($actionLink)) {
            if (str_starts_with($actionLink, 'test:') || $actionLink === 'test') {
                $targetType = 'test';
                $actionLink = 'test';
                $targetId = null;
            } elseif (str_starts_with($actionLink, 'group:') || $actionLink === 'group') {
                $targetType = 'group';
                $actionLink = 'group';
                $targetId = null;
            } elseif (str_starts_with($actionLink, 'event:') || $actionLink === 'event') {
                $targetType = 'event';
                $actionLink = 'event';
                $targetId = null;
            } elseif (str_starts_with($actionLink, 'http://') || str_starts_with($actionLink, 'https://')) {
                $targetType = 'web_url';
                $redirectUrl = $actionLink;
            } else {
                $targetType = 'home';
                $actionLink = 'home';
                $targetId = null;
            }
        } else {
            $actionLink = 'home';
            $targetType = 'home';
            $targetId = null;
        }

        // Ensure inner data object also contains resolved type, message, and target details
        if (is_array($data)) {
            $data['type'] = $data['type'] ?? $type;
            $data['message'] = $data['message'] ?? $message;
            $data['body'] = $data['body'] ?? $message;
            $data['target_type'] = $targetType;
            $data['target_id'] = $targetId ? (string) $targetId : null;
            $data['redirect_url'] = $redirectUrl;
            $data['action_link'] = $actionLink;
        }

        return [
            'id'               => $this->id,
            'type'             => $type,
            'title'            => $data['title'] ?? null,
            'message'          => $message,
            'target_type'      => $targetType,
            'target_id'        => $targetId ? (string) $targetId : null,
            'redirect_url'     => $redirectUrl,
            'banner'           => $data['banner'] ?? ($data['image'] ?? null),
            'action_link'      => $actionLink ?: 'home',
            'is_read'          => $this->read_at !== null,
            'created_at'       => $this->created_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'data'             => $data,
        ];
    }
}
