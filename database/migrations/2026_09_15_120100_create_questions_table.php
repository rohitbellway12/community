<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_level_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'image_based', 'audio_based'])->default('mcq');
            $table->string('image_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->integer('marks')->default(1);
            $table->text('explanation')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
