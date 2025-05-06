<?php

use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\MovimientosInventarioController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedoresController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('layouts/index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('auth')->group(function () {
        Route::resource('proveedores', ProveedoresController::class)->parameters([
            'proveedores' => 'proveedor',
        ]);

        Route::resource('categorias', CategoriasController::class);

        Route::resource('productos', ProductosController::class);
        Route::get('/productos/almacen/{almacen}', [ProductosController::class, 'indexByAlmacen'])
            ->name('productos.indexByAlmacen');

        Route::get('/movimientos', [MovimientosInventarioController::class, 'index'])->name('movimientos.index');
    });

    Route::resource('almacenes', AlmacenesController::class);
});

require __DIR__.'/auth.php';
