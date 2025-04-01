<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\MovimientosInventarioController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;

Route::get('/', function () {
    return view('layouts/index');
});

Route::resource('almacenes', AlmacenesController::class);

Route::resource('productos', ProductosController::class);

Route::resource('movimientosInventario', MovimientosInventarioController::class);

Route::resource('proveedores', ProveedoresController::class)->parameters([
    'proveedores' => 'proveedor',
]);

Route::resource('categorias', CategoriasController::class);

//Route::resource('user', UserController::class);
