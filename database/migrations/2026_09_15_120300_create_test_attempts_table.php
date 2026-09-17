<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('allowed_until')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('total_questions');
            $table->integer('answered_count')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('incorrect_count')->default(0);
            $table->integer('unanswered_count')->default(0);
            $table->decimal('score_obtained', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->enum('result', ['pass', 'fail', 'pending'])->default('pending');
            $table->enum('submission_type', ['manual', 'timeout', 'tab_switch_violation'])->default('manual');
            $table->integer('tab_switch_count')->default(0);
            $table->enum('status', ['active', 'completed', 'disqualified'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
