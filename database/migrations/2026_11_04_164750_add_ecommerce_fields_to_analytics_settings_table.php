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
        Schema::table('analytics_settings', function (Blueprint $table) {
            $table->boolean('ecommerce_enabled')->default(false)->after('is_enabled');
            $table->string('ecommerce_url', 500)->nullable()->after('ecommerce_enabled');
            $table->string('ecommerce_button_label', 100)->nullable()->default('Shop')->after('ecommerce_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_settings', function (Blueprint $table) {
            $table->dropColumn(['ecommerce_enabled', 'ecommerce_url', 'ecommerce_button_label']);
        });
    }
};
