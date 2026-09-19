<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firebase_settings', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->nullable();
            $table->text('credentials_json')->nullable();
            $table->string('server_key')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->string('default_title')->default('Community App');
            $table->string('default_icon')->nullable();
            $table->string('default_color')->default('#0D8ABC');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firebase_settings');
    }
};
