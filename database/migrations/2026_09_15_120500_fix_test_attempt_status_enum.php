<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'disqualified'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->enum('status', ['in_progress', 'completed', 'disqualified'])->default('in_progress')->change();
        });
    }
};
