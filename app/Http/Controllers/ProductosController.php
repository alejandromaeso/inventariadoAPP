<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Models\Categorias;
use App\Models\Productos;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Productos::all(); // Obtener todos los productos
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todas las categorías desde la base de datos
        $categorias = Categorias::all();

        // Pasar las categorías a la vista 'productos.create'
        return view('productos.create', compact('categorias'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos enviados en el formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'required|integer|min:1',
            'categoria_id' => 'required|exists:categorias,id',  // Validar que la categoría existe
        ]);

        // Crear el nuevo producto en la base de datos
        Productos::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'cantidad' => $request->cantidad,
            'categoria_id' => $request->categoria_id,  // Guardar el ID de la categoría
        ]);

        // Redirigir de vuelta a la lista de productos con un mensaje de éxito
        return redirect()->route('productos.index')->with('success', 'Producto creado con éxito.');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Productos::findOrFail($id);
        return view('productos.show', compact('producto'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Productos $productos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductosRequest $request, Productos $productos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Productos $productos)
    {
        //
    }
}
