<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlmacenesRequest;
use App\Http\Requests\UpdateAlmacenesRequest;
use App\Models\Almacenes;
use Illuminate\Http\Request;

class AlmacenesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $almacenes = Almacenes::all(); // Obtener todos los almacenes
        return view('almacenes.index', compact('almacenes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('almacenes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $almacen = Almacenes::findOrFail($id);
        return view('almacenes.show', compact('almacen'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Almacenes $almacenes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlmacenesRequest $request, Almacenes $almacenes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $almacen = Almacenes::findOrFail($id);
        $almacen->delete(); // Eliminar el almacén de la base de datos

        return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado correctamente.');
    }
}
