<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Definimos almacen_id como unsignedBigInteger (igual que la columna id en almacenes)
            // Es nullable porque los administradores no necesitan un almacén asignado (ven todos)
            $table->unsignedBigInteger('almacen_id')->nullable()->after('password');
            // Hacemos un "set null" si lo borramos
            $table->foreign('almacen_id')->references('id')->on('almacenes')->onDelete('set null');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['almacen_id']);
            $table->dropColumn('almacen_id');
        });
    }
};
