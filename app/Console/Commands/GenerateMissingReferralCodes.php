<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateMissingReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-referral-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique referral codes for all users missing one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::whereNull('referral_code')->orWhere('referral_code', '')->get();

        $this->info("Found {$users->count()} users without referral code.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $count = 0;
        foreach ($users as $user) {
            $user->getReferralCodeOrDefault();
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated referral codes for {$count} users!");

        return Command::SUCCESS;
    }
}
