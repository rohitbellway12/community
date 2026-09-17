<?php

namespace App\Notifications;

use App\Models\Test;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Test $test
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $test = $this->test;
        $levelName = $test->testLevel?->name ?? 'Language';

        if ($test->auto_open) {
            $message = "New {$levelName} test '{$test->title}' is now available. Duration: {$test->duration_minutes} min, Passing: {$test->passing_marks}/{$test->total_marks} marks.";
        } elseif ($test->open_date) {
            $start = $test->open_time
                ? $test->open_date->format('M d') . ' at ' . $test->open_time->format('H:i')
                : $test->open_date->format('M d, Y');
            $message = "New {$levelName} test '{$test->title}' has been scheduled. Starts: {$start}. Duration: {$test->duration_minutes} min.";
        } else {
            $message = "New {$levelName} test '{$test->title}' has been created. Duration: {$test->duration_minutes} min.";
        }

        return [
            'type' => 'new_test',
            'title' => 'New Test Available',
            'message' => $message,
            'test_id' => $test->id,
            'test_title' => $test->title,
            'test_level' => $levelName,
            'url' => route('tests.student.show', $test),
        ];
    }
}
