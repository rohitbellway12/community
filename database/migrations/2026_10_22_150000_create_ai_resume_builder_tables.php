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
        // 1. Resumes table
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('My Professional Resume');
            $table->string('template')->default('modern'); // modern, classic, creative
            $table->json('personal_info')->nullable();
            $table->text('summary')->nullable();
            $table->json('education')->nullable();
            $table->json('experience')->nullable();
            $table->json('skills')->nullable();
            $table->json('projects')->nullable();
            $table->json('certifications')->nullable();
            $table->json('languages')->nullable();
            $table->json('custom_sections')->nullable();
            $table->boolean('is_watermark_removed')->default(false);
            $table->timestamp('watermark_removed_at')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 2. Resume Share Verifications
        Schema::create('resume_share_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('platform')->default('whatsapp'); // whatsapp, linkedin, facebook, twitter, instagram, other
            $table->string('verification_method')->default('instant'); // instant, screenshot
            $table->string('proof_image')->nullable();
            $table->string('post_url')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['resume_id', 'status']);
        });

        // 3. Resume Global Settings
        Schema::create('resume_settings', function (Blueprint $table) {
            $table->id();
            $table->text('openai_api_key')->nullable();
            $table->string('openai_model')->default('gpt-4o-mini');
            $table->string('watermark_text')->default('REIAC COMMUNITY • reiac.org');
            $table->boolean('watermark_enabled')->default(true);
            $table->string('verification_mode')->default('both'); // both, instant, manual
            $table->text('reshare_guidelines')->nullable();
            $table->text('reshare_content')->nullable();
            $table->string('reshare_target_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_share_verifications');
        Schema::dropIfExists('resumes');
        Schema::dropIfExists('resume_settings');
    }
};
