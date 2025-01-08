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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');  // nombre (VARCHAR(100), NOT NULL)
            $table->string('descripcion')->nullable();  // descripcion (VARCHAR(255), NULL
            $table->timestamps();
        });
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');  // nombre (VARCHAR(255), NOT NULL)
            $table->string('descripcion')->nullable();  // descripcion (VARCHAR(255), NULL)
            $table->float('precio');  // precio (FLOAT, NOT NULL)
            $table->integer('cantidad');  // cantidad (INT, NOT NULL)
            $table->foreignId('categoria_id')->constrained('categorias');  // categoria_id (INT, FK, NOT NULL)
            $table->timestamps();
        });
        Schema::create('producto_categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');  // Clave foránea a productos
            $table->foreignId('categoria_id')->constrained('categorias');  // Clave foránea a categorias
            $table->timestamps();
        });
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');  // nombre (VARCHAR(255), NOT NULL)
            $table->string('direccion');  // direccion (VARCHAR(255), NOT NULL)
            $table->string('telefono', 9);  // telefono (VARCHAR(9), NOT NULL)
            $table->string('email');  // email (VARCHAR(255), NOT NULL)
            $table->timestamps();
        });
        Schema::create('movimientos_inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');  // producto_id (INT, FK, NOT NULL)
            $table->enum('tipo_movimiento', ['entrada', 'salida']);  // tipo_movimiento (ENUM('entrada', 'salida'))
            $table->integer('cantidad');  // cantidad (INT, NOT NULL)
            $table->date('fecha');  // fecha (DATE, NOT NULL)
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');  // proveedor_id (INT, FK, NULL)
            $table->timestamps();
        });
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');  // nombre (VARCHAR(100), NOT NULL)
            $table->string('ubicacion')->nullable();  // ubicacion (VARCHAR(255), NULL)
            $table->string('descripcion')->nullable();  // descripcion (VARCHAR(255), NULL)
            $table->timestamps();
        });
        Schema::create('productos_almacenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');  // producto_id (INT, FK, NOT NULL)
            $table->foreignId('almacen_id')->constrained('almacenes');  // almacen_id (INT, FK, NOT NULL)
            $table->integer('cantidad');  // cantidad (INT, NOT NULL)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('producto_categorias');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('movimientos_inventarios');
        Schema::dropIfExists('almacenes');
        Schema::dropIfExists('productos_almacenes');
    }
};
