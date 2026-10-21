<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE push_notifications MODIFY COLUMN audience_type VARCHAR(50) NOT NULL DEFAULT 'all_students'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE push_notification_targets MODIFY COLUMN target_type VARCHAR(50) NOT NULL DEFAULT 'all_students'");
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE push_notifications MODIFY COLUMN audience_type ENUM('all_students', 'group', 'individual') NOT NULL DEFAULT 'all_students'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE push_notification_targets MODIFY COLUMN target_type ENUM('all_students', 'group', 'user') NOT NULL DEFAULT 'all_students'");
    }
};
