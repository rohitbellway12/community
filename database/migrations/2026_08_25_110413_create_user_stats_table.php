


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('posts_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('likes_received_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->unsignedBigInteger('followers_count')->default(0);
            $table->unsignedBigInteger('following_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
