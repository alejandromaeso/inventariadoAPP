<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // Tabla pivot para la relación N:M entre productos y proveedores
        Schema::create('productos_proveedores', function (Blueprint $table) {
            $table->id(); // ID propio para la relación

            // Clave foránea para Productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

            $table->foreignId('proveedor_id')
                  ->constrained('proveedores')
                  ->onDelete('cascade');

              // Columna para guardar el precio específico del proveedor para este producto.
            // Lo hacemos nullable si el precio no siempre es obligatorio al crear la relación.
             $table->decimal('precio_proveedor', 10, 2)->nullable();

            $table->timestamps();

            // Evitamos duplicados
            $table->unique(['producto_id', 'proveedor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_proveedores');
    }
};
