<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $userId,
        protected array $tokens,
        protected string $title,
        protected string $body,
        protected array $data = []
    ) {}

    public function handle(PushNotificationService $pushService): void
    {
        if (empty($this->tokens)) {
            return;
        }

        try {
            $sent = $pushService->sendToMany($this->tokens, $this->title, $this->body, $this->data);

            $user = User::find($this->userId);

            if ($user && $sent > 0) {
                $user->deviceTokens()
                    ->active()
                    ->whereIn('token', $this->tokens)
                    ->update(['last_used_at' => now()]);
            }
        } catch (\Throwable $e) {
            Log::warning('SendPushNotificationJob failed: ' . $e->getMessage());
        }
    }
}
