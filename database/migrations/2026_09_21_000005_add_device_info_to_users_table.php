<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('last_seen_at');
            }
            if (!Schema::hasColumn('users', 'device_type')) {
                $table->string('device_type')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('users', 'device_os')) {
                $table->string('device_os')->nullable()->after('device_type');
            }
            if (!Schema::hasColumn('users', 'browser')) {
                $table->string('browser')->nullable()->after('device_os');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'device_os', 'browser', 'ip_address']);
        });
    }
};
