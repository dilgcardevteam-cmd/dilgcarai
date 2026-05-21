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
        Schema::table('users', function (Blueprint $table) {
            $table->string('office_type')->nullable()->after('role'); // Regional, Provincial, City/Municipality
            $table->string('office_name')->nullable()->after('office_type');
            $table->unsignedBigInteger('office_id')->nullable()->after('office_name'); // Reference to PSGC tables
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['office_type', 'office_name', 'office_id']);
        });
    }
};
