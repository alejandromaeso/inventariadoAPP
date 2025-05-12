<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            // Aseguramos que el producto exista y elimina movimientos si el producto se elimina
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            // Aseguramos que el almacén exista y elimina movimientos si el almacén se elimina
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete();
            // Tipo de movimiento: entrada o salida
            $table->enum('tipo', ['entrada', 'salida']);
            // Cantidad movida (siempre positiva)
            $table->unsignedInteger('cantidad');
            // El usuario que realizó la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Motivo o descripción del movimiento
            $table->string('descripcion')->nullable();
            // Fecha creación
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
