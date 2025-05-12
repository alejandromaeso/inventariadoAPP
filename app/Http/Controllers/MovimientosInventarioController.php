<?php

namespace App\Http\Controllers;

use App\Models\MovimientosInventario;
use Illuminate\Http\Request;

class MovimientosInventarioController extends Controller
{
    public function index()
    {
        // Obtenemos todos los movimientos de inventario
        // se usa "with" para evitar problemas de N+1 queries al acceder a sus nombres en la vista.
        // Ordenamos por fecha de creación descendente para ver los más recientes primero.
        // Añadimos paginación si la lista puede ser larga.
        $movimientos = MovimientosInventario::with(['producto', 'almacen', 'user'])
                                            ->latest()
                                            ->paginate(20);

        // Pasamos los movimientos a la vista
        return view('movimientos.index', compact('movimientos'));
    }

    public function create()
    {
        // La creación de movimientos se hace automáticamente desde ProductosController
        abort(404);
    }

    public function store(Request $request)
    {
        // La creación de movimientos se hace automáticamente desde ProductosController
        abort(404);
    }

    public function show(MovimientosInventario $movimientosInventario)
    {
         abort(404);
    }

    public function edit(MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se editan, son un registro histórico.
        abort(404);
    }

    public function update(Request $request, MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se actualizan.
        abort(404);
    }

    public function destroy(MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se eliminan manualmente (excepto por cascada).
        abort(404);
    }
}
