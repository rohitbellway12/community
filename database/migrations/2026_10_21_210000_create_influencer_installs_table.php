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
        Schema::create('influencer_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('influencer_campaigns')->cascadeOnDelete();
            $table->string('device_id', 255);
            $table->string('platform', 30)->default('android');
            $table->string('app_version', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('click_id')->nullable()->constrained('influencer_clicks')->nullOnDelete();
            $table->boolean('is_registered')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['campaign_id', 'device_id']);
            $table->index(['campaign_id', 'created_at']);
            $table->index('is_registered');
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_installs');
    }
};
