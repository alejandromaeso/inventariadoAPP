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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete(); // Asegura que el producto exista y elimina movimientos si el producto se elimina
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete(); // Asegura que el almacén exista y elimina movimientos si el almacén se elimina
            $table->enum('tipo', ['entrada', 'salida']); // Tipo de movimiento: entrada o salida
            $table->unsignedInteger('cantidad'); // Cantidad movida (siempre positiva)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Usuario que realizó la acción (opcional)
            $table->string('descripcion')->nullable(); // Motivo o descripción del movimiento

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
