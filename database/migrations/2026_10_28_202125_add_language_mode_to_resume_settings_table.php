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
        Schema::table('resume_settings', function (Blueprint $table) {
            $table->string('language_mode')->default('both')->after('verification_mode'); // both, en, ko
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_settings', function (Blueprint $table) {
            $table->dropColumn('language_mode');
        });
    }
};
