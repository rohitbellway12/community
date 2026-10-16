<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_settings', function (Blueprint $table) {
            $table->id();
            $table->string('google_analytics_id')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->string('play_store_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->text('custom_head_scripts')->nullable();
            $table->text('custom_body_scripts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_settings');
    }
};
