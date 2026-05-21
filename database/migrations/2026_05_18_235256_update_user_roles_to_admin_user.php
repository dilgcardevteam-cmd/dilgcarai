<?php

use App\Models\User;
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
        User::whereIn('role', ['staff', 'viewer'])->update(['role' => User::ROLE_USER]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
