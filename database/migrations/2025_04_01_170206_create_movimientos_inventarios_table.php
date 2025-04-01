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
        Schema::create('movimientos_inventarios', function (Blueprint $table) {
            $table->id();

            // Clave foránea para Productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

            $table->string('tipo_movimiento');
            $table->integer('cantidad')->unsigned();
            $table->timestamp('fecha')->useCurrent();

            // Clave foránea para Proveedores
            $table->foreignId('proveedor_id')
                  ->nullable()
                  ->constrained('proveedores')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventarios');
    }
};
