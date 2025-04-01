<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlmacenesRequest;
use App\Http\Requests\UpdateAlmacenesRequest;
use App\Models\Almacenes;
use Illuminate\Http\Request;

class AlmacenesController extends Controller
{
    public function index()
    {
        // Obtener todos los almacenes
        $almacenes = Almacenes::all();
        return view('almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255'
        ]);

        Almacenes::create([
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion
        ]);

        return redirect()->route('almacenes.index')->with('success', 'Almacén creado con éxito.');
    }

    public function show($id)
    {
        $almacen = Almacenes::findOrFail($id);
        return view('almacenes.show', compact('almacen'));
    }

    public function edit($id)
    {
        // Buscar el almacén por su ID
        $almacen = Almacenes::findOrFail($id);

        // Pasar el almacén a la vista de edición
        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, $id)
    {
        // Validar los datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Encontrar y actualizar el almacén
        $almacen = Almacenes::findOrFail($id);
        $almacen->update($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy($id)
    {
        $almacen = Almacenes::findOrFail($id);
        // Eliminar el almacén de la base de datos
        $almacen->delete();

        return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado correctamente.');
    }
}
