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

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->default('#1f6feb');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('notebooks', function (Blueprint $table) use ($supportsFullText) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->default('sparkles');
            $table->string('cover_color')->default('#1f6feb');
            $table->string('cover_image_path')->nullable();
            $table->string('visibility')->default('private');
            $table->string('status')->default('active');
            $table->string('shared_token')->nullable()->unique();
            $table->string('ai_title_suggestion')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('smart_tags')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['category_id', 'visibility']);

            if ($supportsFullText) {
                $table->fullText(['title', 'summary', 'description']);
            }
        });

        Schema::create('notebook_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notebook_id')->constrained('notebooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('permission')->default('viewer');
            $table->boolean('can_share')->default(false);
            $table->timestamps();

            $table->unique(['notebook_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notebook_members');
        Schema::dropIfExists('notebooks');
        Schema::dropIfExists('categories');
    }
};
