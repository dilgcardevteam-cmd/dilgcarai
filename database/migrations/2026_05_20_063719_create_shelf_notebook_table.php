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
        Schema::create('shelf_notebook', function (Blueprint $table) {
            $table->foreignId('shelf_id')->constrained()->onDelete('cascade');
            $table->foreignId('notebook_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->primary(['shelf_id', 'notebook_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelf_notebook');
    }
};
