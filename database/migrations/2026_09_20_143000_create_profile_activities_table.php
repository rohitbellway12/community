<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('profile_activities')) {
            Schema::create('profile_activities', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('points')->default(20);
                $table->string('action_url')->nullable();
                $table->string('action_label')->nullable()->default('Complete');
                $table->string('icon')->nullable();
                $table->boolean('status')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });

            // Seed standard default activities
            DB::table('profile_activities')->insert([
                [
                    'key'          => 'avatar',
                    'title'        => 'Upload Profile Photo',
                    'description'  => 'Add a personalized profile avatar to help members recognize you.',
                    'points'       => 20,
                    'action_url'   => '/community/profile/edit',
                    'action_label' => 'Upload Avatar',
                    'icon'         => 'camera',
                    'status'       => true,
                    'sort_order'   => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'key'          => 'bio_location',
                    'title'        => 'Add Bio & Location',
                    'description'  => 'Share a brief introduction about your background and where you are based.',
                    'points'       => 20,
                    'action_url'   => '/community/profile/edit',
                    'action_label' => 'Edit Profile',
                    'icon'         => 'user',
                    'status'       => true,
                    'sort_order'   => 2,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'key'          => 'email_verified',
                    'title'        => 'Verify Email Address',
                    'description'  => 'Confirm your email address to secure your account and receive updates.',
                    'points'       => 20,
                    'action_url'   => '/community/profile/edit',
                    'action_label' => 'Verify Email',
                    'icon'         => 'mail',
                    'status'       => true,
                    'sort_order'   => 3,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'key'          => 'first_post',
                    'title'        => 'Publish Your First Post',
                    'description'  => 'Start a meaningful discussion or share knowledge with the community.',
                    'points'       => 20,
                    'action_url'   => '/community',
                    'action_label' => 'Create Post',
                    'icon'         => 'pencil',
                    'status'       => true,
                    'sort_order'   => 4,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'key'          => 'first_test',
                    'title'        => 'Attempt a Student Test',
                    'description'  => 'Take at least one student test to test your skills and earn scores.',
                    'points'       => 20,
                    'action_url'   => '/community/tests/student',
                    'action_label' => 'Take Test',
                    'icon'         => 'academic-cap',
                    'status'       => true,
                    'sort_order'   => 5,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_activities');
    }
};
