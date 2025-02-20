<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        // Obtener todas las categorías de la base de datos
        $categorias = Categorias::all();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        // Mostrar el formulario para crear una categoría
        return view('categorias.create');
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias'
        ]);

        // Crear una nueva categoría
        Categorias::create([
            'nombre' => $request->nombre
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría creada con éxito.');
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $categoria = Categorias::findOrFail($id);
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $categoria = Categorias::findOrFail($id);
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified category in the database.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categorias::findOrFail($id);

        // Validar la entrada
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id
        ]);

        // Actualizar la categoría
        $categoria->update([
            'nombre' => $request->nombre
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada con éxito.');
    }

    /**
     * Remove the specified category from the database.
     */
    public function destroy($id)
    {
        $categoria = Categorias::findOrFail($id);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada con éxito.');
    }
}
