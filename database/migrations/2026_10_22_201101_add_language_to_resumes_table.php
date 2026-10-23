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
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('language')->default('en')->after('template');
            $table->json('military_service')->nullable()->after('custom_sections');
            $table->json('self_introduction')->nullable()->after('military_service');
            $table->json('language_tests')->nullable()->after('self_introduction');
            $table->json('computer_skills')->nullable()->after('language_tests');
            $table->json('awards')->nullable()->after('computer_skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn([
                'language',
                'military_service',
                'self_introduction',
                'language_tests',
                'computer_skills',
                'awards'
            ]);
        });
    }
};
