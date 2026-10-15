<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 65);
            $table->string('description', 240);
            $table->string('image_url', 2048)->nullable();
            $table->string('banner_url', 2048)->nullable();
            $table->string('action_link', 1024)->nullable();
            $table->enum('audience_type', ['all_students', 'group', 'individual'])->default('all_students');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'partially_failed', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('scheduled_at');
        });

        Schema::create('push_notification_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('push_notifications')->cascadeOnDelete();
            $table->enum('target_type', ['all_students', 'group', 'user'])->default('all_students');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'target_type']);
        });

        Schema::create('push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('push_notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device_token', 500)->nullable();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_logs');
        Schema::dropIfExists('push_notification_targets');
        Schema::dropIfExists('push_notifications');
    }
};
