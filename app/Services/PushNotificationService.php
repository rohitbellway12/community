<?php

namespace App\Services;

use App\Models\FirebaseSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Resolve Firebase config: database (admin-managed) takes priority,
     * falling back to .env config when not set in the database.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        $settings = cache()->remember('push_notification_config', 300, function () {
            return FirebaseSetting::config();
        });

        return match ($key) {
            'is_enabled'       => $settings->is_enabled ?? false,
            'project_id'       => $settings->project_id       ?: config('fcm.project_id'),
            'credentials_json' => $settings->credentials_json ?: config('fcm.credentials_json'),
            'server_key'       => $settings->server_key       ?: config('fcm.server_key'),
            'default_title'    => $settings->default_title    ?: config('fcm.default_title', 'Community App'),
            'default_icon'     => $settings->default_icon     ?: config('fcm.default_icon', 'notification_icon'),
            'default_color'    => $settings->default_color    ?: config('fcm.default_color', '#0D8ABC'),
            default             => $default,
        };
    }

    public function isEnabled(): bool
    {
        return (bool) $this->config('is_enabled', false);
    }

    protected function projectId(): string
    {
        return $this->config('project_id', '');
    }

    protected function credentialsJson(): ?string
    {
        return $this->config('credentials_json');
    }

    protected function serverKey(): ?string
    {
        return $this->config('server_key');
    }

    /**
     * Send a push notification to a single FCM token.
     */
    public function sendToDevice(
        string $token,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): bool {
        $result = $this->sendToDeviceDetailed($token, $title, $body, $data, $imageUrl);
        return $result['success'];
    }

    /**
     * Send a push notification with detailed error/result reporting.
     */
    public function sendToDeviceDetailed(
        string $token,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'Push notification service is disabled in settings.'];
        }

        $stringData = $this->stringifyData(array_merge([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], $data));

        if ($this->credentialsJson()) {
            return $this->sendViaV1WithResult($token, $title, $body, $stringData, $imageUrl);
        }

        return $this->sendViaLegacyWithResult($token, $title, $body, $stringData, $imageUrl);
    }

    /**
     * Send to multiple tokens (sequential; suitable for small batches).
     */
    public function sendToMany(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): int {
        $sent = 0;
        foreach ($tokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data, $imageUrl)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * FCM HTTP v1 API using OAuth2 service-account JWT with result.
     */
    protected function sendViaV1WithResult(
        string $token,
        string $title,
        string $body,
        array $data,
        ?string $imageUrl = null
    ): array {
        $accessToken = $this->getAccessTokenV1();

        if (!$accessToken) {
            Log::warning('FCM push failed: could not obtain access token.');
            return ['success' => false, 'error' => 'Could not obtain OAuth2 access token.'];
        }

        $notificationPayload = [
            'title' => $title,
            'body'  => $body,
        ];
        if (!empty($imageUrl)) {
            $notificationPayload['image'] = $imageUrl;
        }

        $androidNotification = [
            'icon'         => $this->config('default_icon', 'notification_icon'),
            'color'        => $this->config('default_color', '#0D8ABC'),
            'click_action' => $data['click_action'] ?? 'FLUTTER_NOTIFICATION_CLICK',
        ];
        if (!empty($imageUrl)) {
            $androidNotification['image'] = $imageUrl;
        }

        $apnsPayload = [
            'aps' => [
                'badge'           => 1,
                'sound'           => 'default',
                'mutable-content' => 1,
            ],
        ];
        if (!empty($imageUrl)) {
            $apnsPayload['fcm_options'] = ['image' => $imageUrl];
        }

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => $notificationPayload,
                'data'         => $data,
                'android'      => [
                    'notification' => $androidNotification,
                ],
                'apns'         => [
                    'payload' => $apnsPayload,
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type'  => 'application/json',
                'project_id'    => $this->projectId(),
            ])
            ->post("https://fcm.googleapis.com/v1/projects/" . $this->projectId() . "/messages:send", $payload);

        if ($response->failed()) {
            $bodyStr = $response->body();
            Log::warning('FCM v1 push failed: ' . $bodyStr);

            // Deactivate invalid or unregistered token
            if (str_contains($bodyStr, 'UNREGISTERED') || $response->status() === 404) {
                \App\Models\DeviceToken::where('token', $token)->update(['is_active' => false]);
            }

            return ['success' => false, 'error' => $bodyStr];
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * Backward compatible wrapper for sendViaV1
     */
    protected function sendViaV1(string $token, string $title, string $body, array $data): bool
    {
        $res = $this->sendViaV1WithResult($token, $title, $body, $data);
        return $res['success'];
    }

    /**
     * FCM legacy HTTP API with result.
     */
    protected function sendViaLegacyWithResult(
        string $token,
        string $title,
        string $body,
        array $data,
        ?string $imageUrl = null
    ): array {
        $notification = [
            'title' => $title,
            'body'  => $body,
        ];
        if (!empty($imageUrl)) {
            $notification['image'] = $imageUrl;
        }

        $payload = [
            'to'           => $token,
            'notification' => $notification,
            'data'         => $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->serverKey(),
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if ($response->failed()) {
            $err = $response->body();
            Log::warning('FCM legacy push failed: ' . $err);
            return ['success' => false, 'error' => $err];
        }

        $result = $response->json();
        if (isset($result['failure']) && $result['failure'] > 0) {
            $errMsg = $result['results'][0]['error'] ?? 'Legacy FCM sending error';
            if (in_array($errMsg, ['NotRegistered', 'InvalidRegistration'], true)) {
                \App\Models\DeviceToken::where('token', $token)->update(['is_active' => false]);
            }
            return ['success' => false, 'error' => $errMsg];
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * FCM legacy HTTP API (server key).
     */
    protected function sendViaLegacy(string $token, string $title, string $body, array $data): bool
    {
        $res = $this->sendViaLegacyWithResult($token, $title, $body, $data);
        return $res['success'];
    }

    /**
     * Obtain an OAuth2 access token via a service-account signed JWT.
     */
    protected function getAccessTokenV1(): ?string
    {
        $credentials = json_decode($this->credentialsJson(), true);

        if (!$credentials || empty($credentials['private_key']) || empty($credentials['client_email'])) {
            return null;
        }

        $now = time();
        $payload = [
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ];

        $jwt = $this->encodeJwt($payload, $credentials['private_key']);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        $token = $response->json('access_token');

        if ($response->failed() || empty($token)) {
            Log::warning('FCM token exchange failed: ' . $response->body());
            return null;
        }

        return $token;
    }

    /**
     * Build a signed RS256 JWT.
     */
    protected function encodeJwt(array $payload, string $privateKey): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $body   = $this->base64UrlEncode(json_encode($payload));

        $unsigned = $header . '.' . $body;

        openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $unsigned . '.' . $this->base64UrlEncode($signature);
    }

    protected function base64UrlEncode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    /**
     * FCM data values must be strings.
     */
    protected function stringifyData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $result[$key] = (string) $value;
        }

        return $result;
    }
}
