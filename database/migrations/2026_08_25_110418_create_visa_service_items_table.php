<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_service_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_service_id')->constrained()->cascadeOnDelete();
            $table->string('visa_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_service_items');
    }
};
