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
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
        });

        // Migrate existing document files if any
        $existingDocs = DB::table('documents')->whereNotNull('file_path')->get();
        foreach ($existingDocs as $doc) {
            DB::table('document_files')->insert([
                'document_id'    => $doc->id,
                'file_path'      => $doc->file_path,
                'file_name'      => $doc->file_name,
                'file_type'      => $doc->file_type,
                'file_size'      => $doc->file_size,
                'mime_type'      => $doc->mime_type,
                'download_count' => $doc->download_count ?? 0,
                'created_at'     => $doc->created_at ?? now(),
                'updated_at'     => $doc->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_files');
    }
};
