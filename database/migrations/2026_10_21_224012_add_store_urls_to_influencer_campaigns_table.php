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
        Schema::table('influencer_campaigns', function (Blueprint $table) {
            $table->text('play_store_url')->nullable()->after('destination_url');
            $table->text('app_store_url')->nullable()->after('play_store_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('influencer_campaigns', function (Blueprint $table) {
            $table->dropColumn(['play_store_url', 'app_store_url']);
        });
    }
};
