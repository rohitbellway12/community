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
        Schema::create('result_slabs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "A++", "Grade A", "Pass"
            $table->decimal('min_marks', 8, 2)->default(0);
            $table->decimal('max_marks', 8, 2)->default(0);
            $table->string('badge_color')->default('emerald'); // emerald, blue, amber, purple, rose, slate
            $table->text('description')->nullable();
            $table->foreignId('test_id')->nullable()->constrained('tests')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_slabs');
    }
};
