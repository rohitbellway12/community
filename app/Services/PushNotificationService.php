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
            'project_id'       => $settings->project_id       ?: config('fcm.project_id'),
            'credentials_json' => $settings->credentials_json ?: config('fcm.credentials_json'),
            'server_key'       => $settings->server_key       ?: config('fcm.server_key'),
            'default_title'    => $settings->default_title    ?: config('fcm.default_title', 'Community App'),
            'default_icon'     => $settings->default_icon     ?: config('fcm.default_icon', 'notification_icon'),
            'default_color'    => $settings->default_color    ?: config('fcm.default_color', '#0D8ABC'),
            default             => $default,
        };
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
        array $data = []
    ): bool {
        $stringData = $this->stringifyData(array_merge([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], $data));

        if ($this->credentialsJson()) {
            return $this->sendViaV1($token, $title, $body, $stringData);
        }

        return $this->sendViaLegacy($token, $title, $body, $stringData);
    }

    /**
     * Send to multiple tokens (sequential; suitable for small batches).
     */
    public function sendToMany(
        array $tokens,
        string $title,
        string $body,
        array $data = []
    ): int {
        $sent = 0;
        foreach ($tokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * FCM HTTP v1 API using OAuth2 service-account JWT.
     */
    protected function sendViaV1(string $token, string $title, string $body, array $data): bool
    {
        $accessToken = $this->getAccessTokenV1();

        if (!$accessToken) {
            Log::warning('FCM push failed: could not obtain access token.');
            return false;
        }

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
                'android' => [
                    'notification' => [
                        'icon'  => $this->config('default_icon', 'notification_icon'),
                        'color' => $this->config('default_color', '#0D8ABC'),
                        'click_action' => $data['click_action'] ?? 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'badge' => 1,
                            'sound' => 'default',
                        ],
                    ],
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
            Log::warning('FCM v1 push failed: ' . $response->body());
        }

        return $response->successful();
    }

    /**
     * FCM legacy HTTP API (server key).
     */
    protected function sendViaLegacy(string $token, string $title, string $body, array $data): bool
    {
        $payload = [
            'to'           => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data'   => $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->serverKey(),
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if ($response->failed()) {
            Log::warning('FCM legacy push failed: ' . $response->body());
        }

        return $response->successful();
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

        if ($response->failed() || !$response->has('access_token')) {
            Log::warning('FCM token exchange failed: ' . $response->body());
            return null;
        }

        return $response->json('access_token');
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
