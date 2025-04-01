<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
         // Tabla pivot para la relación N:M entre productos y categorías
        Schema::create('producto_categoria', function (Blueprint $table) {
            $table->id();

             // Clave foránea para Productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

             // Clave foránea para Categorías
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('cascade');

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['producto_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_categoria');
    }
};
