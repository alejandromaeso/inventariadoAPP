<?php

use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\MovimientosInventarioController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

//Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
});

//Ruta de /home para autenticado
Route::get('/home', function () {
    return view('layouts/index');
})->middleware(['auth', 'verified'])->name('home');

//Rutas del middleware estando autenticado
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('almacenes', AlmacenesController::class);
    Route::get('/productos/almacen/{almacen}', [ProductosController::class, 'indexByAlmacen'])
    ->name('productos.indexByAlmacen');
    Route::resource('productos', ProductosController::class);

    // Middleware siendo administrador y teniendo que estar logueado
    Route::middleware('admin')->group(function () {
        Route::resource('proveedores', ProveedoresController::class)->parameters([
            'proveedores' => 'proveedor',
        ]);
        Route::resource('categorias', CategoriasController::class);
        Route::get('/movimientos', [MovimientosInventarioController::class, 'index'])->name('movimientos.index');

        Route::get('register', [UsersController::class, 'create'])
            ->name('register');

        Route::post('register', [UsersController::class, 'store']);
        Route::get('/usuarios', [UsersController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/{user}/editar', [UsersController::class, 'edit'])->name('usuarios.edit');
        Route::patch('/usuarios/{user}', [UsersController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UsersController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__ . '/auth.php';
