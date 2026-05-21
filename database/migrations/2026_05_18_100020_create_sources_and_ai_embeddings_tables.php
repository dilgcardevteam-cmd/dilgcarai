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
        $supportsFullText = Schema::getConnection()->getDriverName() !== 'sqlite';

        Schema::create('sources', function (Blueprint $table) use ($supportsFullText) {
            $table->id();
            $table->foreignId('notebook_id')->constrained('notebooks')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('storage_disk')->default('public');
            $table->string('storage_path')->nullable();
            $table->string('source_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status')->default('queued');
            $table->string('content_hash')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->text('summary')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            $table->timestamps();

            $table->index(['notebook_id', 'status']);

            if ($supportsFullText) {
                $table->fullText(['name', 'extracted_text']);
            }
        });

        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notebook_id')->constrained('notebooks')->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('sources')->cascadeOnDelete();
            $table->unsignedBigInteger('message_id')->nullable()->index();
            $table->unsignedInteger('chunk_index')->default(0);
            $table->string('content_hash')->index();
            $table->string('embedding_model')->nullable();
            $table->unsignedInteger('token_count')->default(0);
            $table->longText('content');
            $table->json('embedding')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['notebook_id', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
        Schema::dropIfExists('sources');
    }
};
