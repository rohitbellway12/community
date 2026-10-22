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
        Schema::create('influencer_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('influencer_name');
            $table->string('campaign_name');
            $table->string('code')->unique();
            $table->string('channel')->default('other');
            $table->text('destination_url');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('influencer_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('influencer_campaigns')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_suspicious')->default(false);
            $table->json('suspicion_reasons')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['campaign_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('is_suspicious');
            $table->index('is_bot');
        });

        Schema::create('influencer_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('influencer_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('click_id')->nullable()->constrained('influencer_clicks')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_id', 255)->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->json('suspicion_reasons')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['campaign_id', 'user_id']);
            $table->index(['campaign_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('device_id');
            $table->index('is_suspicious');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_registrations');
        Schema::dropIfExists('influencer_clicks');
        Schema::dropIfExists('influencer_campaigns');
    }
};
