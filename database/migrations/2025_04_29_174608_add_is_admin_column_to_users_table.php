<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the is_admin boolean column, default is false
            $table->boolean('is_admin')->default(false)->after('password'); // You can adjust the position
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the is_admin column if rolling back
            $table->dropColumn('is_admin');
        });
    }
};
