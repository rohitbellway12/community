<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::call(function () {
    $dueNotifications = \App\Models\PushNotification::scheduled()->get();
    foreach ($dueNotifications as $notification) {
        $notification->update(['status' => 'draft']);
        \App\Jobs\SendAdminPushBroadcastJob::dispatch($notification);
    }
})->everyMinute()->name('send-scheduled-push-notifications');

\Illuminate\Support\Facades\Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->name('process-queued-jobs');


