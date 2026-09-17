<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestAttempt;

class ProcessExpiredAttempts extends Command
{
    protected $signature = 'tests:process-expired';

    protected $description = 'Auto-submit timed-out test attempts';

    public function handle(): int
    {
        $expiredAttempts = TestAttempt::where('status', 'active')
            ->whereNotNull('allowed_until')
            ->where('allowed_until', '<', now())
            ->get();

        if ($expiredAttempts->isEmpty()) {
            $this->info('No expired attempts found.');
            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($expiredAttempts as $attempt) {
            $attempt->gradeAndComplete(null, 'timeout');
            $processed++;
            $this->info("Auto-submitted attempt #{$attempt->id} (Test: {$attempt->test->title}, Student: {$attempt->user->name})");
        }

        $this->info("Total processed: {$processed}");

        return self::SUCCESS;
    }
}
