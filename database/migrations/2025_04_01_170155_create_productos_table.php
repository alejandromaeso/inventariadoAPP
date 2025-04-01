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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2)->unsigned()->default(0.00);

            // Clave foránea para Categorias (relación belongsTo)
            $table->foreignId('categoria_id')
                  ->nullable() // Producto puede no tener categoría
                  ->constrained('categorias')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
        });
        Schema::dropIfExists('productos');
    }
};
