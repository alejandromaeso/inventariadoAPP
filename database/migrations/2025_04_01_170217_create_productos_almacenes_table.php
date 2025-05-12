<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla pivot para la relación N:M entre productos y almacenes
        Schema::create('productos_almacenes', function (Blueprint $table) {
            $table->id(); // ID propio para la relación

            // Clave foránea para Productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

            // Clave foránea para Almacenes
            $table->foreignId('almacen_id')
                  ->constrained('almacenes')
                  ->onDelete('cascade');

            // Campo extra en la tabla pivot
            $table->integer('cantidad')->unsigned()->default(0);

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['producto_id', 'almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_almacenes');
    }
};
