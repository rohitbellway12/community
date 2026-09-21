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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // User who shared their referral code
            $table->foreignId('referrer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // User who signed up using the referral code
            $table->foreignId('referred_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Prevent the same user from being referred twice
            $table->unique('referred_id');

            // Index for fast lookup of who referred whom
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
