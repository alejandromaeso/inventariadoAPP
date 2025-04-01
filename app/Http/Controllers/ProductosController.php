<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Categorias;
use App\Models\Almacenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Para validaciones más complejas si es necesario
use Illuminate\Validation\Rule;

class ProductosController extends Controller
{
    public function index()
    {
        // Cargamos relaciones necesarias para la vista
        $productos = Productos::with('categoria', 'almacenes')
                             // Calculamos la suma de 'cantidad' en la tabla pivote 'productos_almacenes'
                             // y la asignamos a una nueva propiedad virtual llamada 'stock_total'
                             ->withSum('almacenes as stock_total', 'productos_almacenes.cantidad')
                             // Ordenar por más reciente
                             ->latest()
                              // Usamos paginación
                             ->paginate(15);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        // Ordenamos alfabéticamente
        $categorias = Categorias::orderBy('nombre')->get();
        $almacenes = Almacenes::orderBy('nombre')->get();
        return view('productos.create', compact('categorias', 'almacenes'));
    }

    public function store(Request $request)
    {
        // --- Validación ---
        $validatedData = $request->validate([
            // Evitar nombres duplicados poniendolos unique
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',

            // Validación para almacenes y cantidades
            // Debemos seleccionar al menos un almacén
            'almacenes' => 'required|array|min:1',
            // Cada ID de almacén debe existir
            'almacenes.*' => 'required|integer|exists:almacenes,id',
            // El array de cantidades es necesario
            'cantidades' => 'required|array',
            // Valida que cada cantidad enviada corresponda a un almacén seleccionado y sea un número >= 0
            'cantidades.*' => [
                'required',
                'integer',
                'min:0',
                // Asegura que la clave del array 'cantidades' (que es el ID del almacén)
                // esté presente en el array 'almacenes' enviado.
                function ($attribute, $value, $fail) use ($request) {
                    // Extrae el ID del almacén de la clave del atributo
                    $almacenId = explode('.', $attribute)[1];
                    if (!in_array($almacenId, $request->input('almacenes', []))) {
                        $fail("La cantidad para el almacén ID {$almacenId} no corresponde a un almacén seleccionado.");
                    }
                },
            ],
        ], [
            // Mensajes de error personalizados
            'almacenes.required' => 'Debes seleccionar y asignar cantidad al menos a un almacén.',
            'almacenes.min' => 'Debes seleccionar y asignar cantidad al menos a un almacén.',
            'cantidades.*.required' => 'Debes indicar una cantidad para cada almacén seleccionado.',
            'cantidades.*.min' => 'La cantidad para cada almacén debe ser 0 o mayor.',
        ]);

        // Iniciamos transacción
        try {
            DB::beginTransaction();

            // Creamos el producto principal
            // Excluimos 'almacenes' y 'cantidades' que no son columnas directas
            $producto = Productos::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'precio' => $validatedData['precio'],
                'categoria_id' => $validatedData['categoria_id'],
            ]);

            // Preparamos datos para la tabla pivote (sync)
            $syncData = [];
            $cantidadesInput = $validatedData['cantidades'];

            // Iteramos SOLO sobre los almacenes seleccionados
            foreach ($validatedData['almacenes'] as $almacenId) {
                // Usamos el ID del almacén como clave para buscar su cantidad
                // El default a 0 es por si acaso, aunque la validación debe cubrirlo
                $cantidadParaEsteAlmacen = $cantidadesInput[$almacenId] ?? 0;
                $syncData[$almacenId] = ['cantidad' => $cantidadParaEsteAlmacen];
            }

            // Añadimos las entradas en la tabla pivote
            if (!empty($syncData)) {
                $producto->almacenes()->sync($syncData);
            }

            DB::commit();

            return redirect()->route('productos.index')->with('success', 'Producto creado y stock inicial asignado correctamente.');

        } catch (\Exception $e) {
            // Revertimos en caso de error
            DB::rollBack();
            return back()->withInput()->withErrors(['error_inesperado' => 'Ocurrió un error al guardar el producto: ' . $e->getMessage()]);
        }
    }

    public function show(Productos $producto)
    {
        // Cargamos relaciones si no lo hacen automáticamente
        $producto->load('categoria', 'almacenes');
        return view('productos.show', compact('producto'));
    }

    public function edit(Productos $producto)
    {
        // Cargamos almacenes actuales para el formulario
        $producto->load('almacenes');
        $categorias = Categorias::orderBy('nombre')->get();
        $almacenes = Almacenes::orderBy('nombre')->get();

        // Creamos un array asociativo de almacenes actuales para fácil acceso en la vista
        $almacenesActuales = $producto->almacenes->mapWithKeys(function ($almacen) {
            return [$almacen->id => $almacen->pivot->cantidad];
        });

        return view('productos.edit', compact('producto', 'categorias', 'almacenes', 'almacenesActuales'));
    }

    public function update(Request $request, Productos $producto)
    {
         // --- Validación ---
         // Permitimos que el nombre sea el mismo del producto actual
         $validatedData = $request->validate([
            'nombre' => ['required','string','max:255', Rule::unique('productos')->ignore($producto->id)],
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'almacenes' => 'nullable|array',
            'almacenes.*' => 'required|integer|exists:almacenes,id',
            'cantidades' => 'nullable|array',
             // Valida cantidad si se envía el almacén correspondiente
            'cantidades.*' => [
                'required_with:almacenes.*',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $almacenId = explode('.', $attribute)[1];
                    // Verifica si este ID de almacén está en la lista de almacenes enviados
                    if ($request->has('almacenes') && !in_array($almacenId, $request->input('almacenes', []))) {
                         // Se envió cantidad para un almacén no seleccionado en esta request
                         $fail("Cantidad inválida para el almacén ID {$almacenId}.");
                    }
                },
             ],
        ]);

        try {
            DB::beginTransaction();

            // Actualizamos el producto principal
            $producto->update([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'precio' => $validatedData['precio'],
                'categoria_id' => $validatedData['categoria_id'],
            ]);

            // Preparamos datos para sync
            $syncData = [];
            // Solo si se enviaron almacenes
            if ($request->has('almacenes')) {
                 $cantidadesInput = $request->input('cantidades', []);
                 foreach ($validatedData['almacenes'] as $almacenId) {
                     $cantidadParaEsteAlmacen = $cantidadesInput[$almacenId] ?? 0;
                     $syncData[$almacenId] = ['cantidad' => $cantidadParaEsteAlmacen];
                 }
            }
            // Actualizamos el pivote
            // sync() se encarga de añadir, actualizar y quitar relaciones según $syncData
            $producto->almacenes()->sync($syncData);

            DB::commit();

            return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
             // Opcional: Loguear el error: Log::error($e);
            return back()->withInput()->withErrors(['error_inesperado' => 'Ocurrió un error al actualizar el producto: ' . $e->getMessage()]);
        }
    }


    public function destroy(Productos $producto)
    {
        try {
            DB::beginTransaction();
            // detach() quita las relaciones en la tabla pivote antes de borrar
            $producto->almacenes()->detach();
            // Borramos el producto
            $producto->delete();
            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('productos.index')->with('error', 'No se pudo eliminar el producto: '.$e->getMessage());
        }
    }
}
