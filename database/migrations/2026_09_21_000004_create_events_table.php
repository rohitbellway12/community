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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Event identity
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('rules')->nullable(); // HTML content for rules & regulations page

            // Banner info (for home top bar)
            $table->string('banner_image')->nullable();
            $table->string('banner_link_url')->nullable(); // Points to event rules page by default

            // Manual event timing (admin controlled)
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // Scoring weights (admin configurable)
            $table->unsignedInteger('posts_weight')->default(5);
            $table->unsignedInteger('comments_weight')->default(3);
            $table->unsignedInteger('likes_weight')->default(2);
            $table->unsignedInteger('referrals_weight')->default(10);

            // Status
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
