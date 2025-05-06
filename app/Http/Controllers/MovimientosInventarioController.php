<?php

namespace App\Http\Controllers;

use App\Models\MovimientosInventario; // Importa el modelo
use Illuminate\Http\Request;
// Opcional: Importar los modelos relacionados si los necesitas directamente en el controlador,
// aunque con eager loading en el index, no es estrictamente necesario aquí.
// use App\Models\Productos;
// use App\Models\Almacenes;
// use App\Models\User;

class MovimientosInventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los movimientos de inventario
        // Usamos 'with' para eager load las relaciones (producto, almacen, user)
        // y evitar problemas de N+1 queries al acceder a sus nombres en la vista.
        // Ordenamos por fecha de creación descendente para ver los más recientes primero.
        // Añadimos paginación si la lista puede ser larga.
        $movimientos = MovimientosInventario::with(['producto', 'almacen', 'user'])
                                            ->latest() // Ordena por created_at DESC
                                            ->paginate(20); // Pagina 20 movimientos por página

        // Pasar los movimientos a la vista
        return view('movimientos.index', compact('movimientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // La creación de movimientos se hace automáticamente desde ProductosController
        // No necesitas una vista o lógica de creación manual para movimientos aquí.
        abort(404); // O redirigir a otro lugar, ya que no hay formulario de creación directa.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // La creación de movimientos se hace automáticamente desde ProductosController
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(MovimientosInventario $movimientosInventario)
    {
        // Puedes mostrar detalles de un movimiento específico si es necesario.
        // Por ahora, asumimos que la lista en index es suficiente.
        // Para una vista de detalle, necesitarías cargar relaciones si no están ya.
        // $movimientosInventario->load(['producto', 'almacen', 'user']);
        // return view('movimientos.show', compact('movimientosInventario'));
         abort(404); // O implementar una vista de detalle
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se editan, son un registro histórico.
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se actualizan.
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MovimientosInventario $movimientosInventario)
    {
        // Los movimientos no se eliminan manualmente (excepto por cascada).
        abort(404);
    }
}
