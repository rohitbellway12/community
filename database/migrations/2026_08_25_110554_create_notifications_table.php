<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');

            // morphs() automatically indexes notifiable_type and notifiable_id
            $table->morphs('notifiable');

            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Duplicate index line hata di gayi hai
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
