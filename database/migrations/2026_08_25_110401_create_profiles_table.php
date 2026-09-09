<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('username')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('location')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->index('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
