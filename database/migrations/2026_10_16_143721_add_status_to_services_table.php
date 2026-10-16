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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'status')) {
                $table->string('status', 20)->default('active')->after('sort_order');
                $table->index(['status', 'sort_order']);
            }
        });

        // Sync existing data
        if (Schema::hasColumn('services', 'status') && Schema::hasColumn('services', 'is_published')) {
            DB::table('services')->where('is_published', 1)->update(['status' => 'active']);
            DB::table('services')->where('is_published', 0)->update(['status' => 'inactive']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'status')) {
                $table->dropIndex(['status', 'sort_order']);
                $table->dropColumn('status');
            }
        });
    }
};
