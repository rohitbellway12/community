<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. In MariaDB/MySQL, a timestamp column created without nullable() receives
        // DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP by default.
        // This caused started_at to be automatically updated to the current time whenever an attempt was submitted or updated.
        // We alter started_at to TIMESTAMP NULL DEFAULT NULL to remove the auto-update trigger.
        DB::statement("ALTER TABLE `test_attempts` MODIFY `started_at` TIMESTAMP NULL DEFAULT NULL");

        // 2. Restore true started_at for existing attempts where started_at was overwritten with the submission time (started_at > created_at)
        DB::statement("UPDATE `test_attempts` SET `started_at` = `created_at` WHERE `started_at` > `created_at`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we do not want to restore the broken ON UPDATE CURRENT_TIMESTAMP behavior
    }
};
