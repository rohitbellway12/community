<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->enum('target_type', ['all', 'group', 'user'])->default('all')->after('status');
            $table->foreignId('target_group_id')->nullable()->after('target_type')->constrained('groups')->nullOnDelete();
            $table->foreignId('target_user_id')->nullable()->after('target_group_id')->constrained('users')->nullOnDelete();
            $table->boolean('require_camera_photo')->default(false)->after('target_user_id');
            $table->string('agency_name')->nullable()->after('require_camera_photo');
            $table->string('controller_name')->nullable()->after('agency_name');
            $table->string('director_name')->nullable()->after('controller_name');
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->string('candidate_photo')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['target_group_id']);
            $table->dropForeign(['target_user_id']);
            $table->dropColumn([
                'target_type',
                'target_group_id',
                'target_user_id',
                'require_camera_photo',
                'agency_name',
                'controller_name',
                'director_name',
            ]);
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn('candidate_photo');
        });
    }
};
