<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Categorias;
use App\Models\Almacenes;
use App\Models\Proveedores;
use App\Models\MovimientosInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductosController extends Controller
{

    public function index()
    {
        // Cargamos los productos
        $productos = Productos::with(['categoria', 'almacenes', 'proveedores'])
                           ->withSum('almacenes as stock_total', 'productos_almacenes.cantidad')
                           ->latest()
                           ->paginate(15);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categorias::orderBy('nombre')->get();
        $almacenes = Almacenes::orderBy('nombre')->get();
        $proveedores = Proveedores::orderBy('nombre')->get();

        return view('productos.create', compact('categorias', 'almacenes', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',

            'almacen_stock' => ['present', 'array'],
            'almacen_stock.*' => ['required', 'integer', 'min:0'],

            'proveedor_precio' => ['present', 'array'],
            'proveedor_precio.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ], [
            //Posibles errores
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'precio.required' => 'El precio de venta es obligatorio.',
            'precio.numeric' => 'El precio de venta debe ser un número.',
            'precio.min' => 'El precio de venta no puede ser negativo.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'almacen_stock.array' => 'Los datos de stock deben enviarse en el formato correcto.',
            'almacen_stock.*.required' => 'Se requiere una cantidad (incluso 0) para cada almacén asignado.',
            'almacen_stock.*.integer' => 'La cantidad de stock debe ser un número entero.',
            'almacen_stock.*.min' => 'La cantidad de stock no puede ser negativa.',
            'proveedor_precio.array' => 'Los datos de precios de proveedor deben enviarse en el formato correcto.',
            'proveedor_precio.*.numeric' => 'El precio del proveedor debe ser un número.',
            'proveedor_precio.*.min' => 'El precio del proveedor no puede ser negativo.',
            'proveedor_precio.*.max' => 'El precio del proveedor es demasiado alto.',
        ]);

        // El precio de venta tiene que ser mayor que el precio del proveedor
        $validator->after(function ($validator) {
            $validatedData = $validator->validated();
            $sellingPrice = $validatedData['precio'] ?? null;

            if (is_numeric($sellingPrice) && $sellingPrice >= 0) {
                $supplierPrices = $validatedData['proveedor_precio'] ?? [];

                foreach ($supplierPrices as $proveedorId => $precioProveedor) {
                     if (is_numeric($precioProveedor) && $precioProveedor !== null && $precioProveedor !== '') {
                        if ($sellingPrice <= $precioProveedor) {
                             $validator->errors()->add(
                                'precio',
                                "El precio de venta ({$sellingPrice}) debe ser mayor que el precio del proveedor con ID {$proveedorId} ({$precioProveedor})."
                             );
                        }
                     }
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Validación Adicional (existencia de IDs)
        $stockInput = $validatedData['almacen_stock'] ?? [];
        $almacenIdsEnviados = array_keys($stockInput);
        if (!empty($almacenIdsEnviados)) {
            $almacenesExistentesIds = Almacenes::whereIn('id', $almacenIdsEnviados)->pluck('id')->toArray();
            $idsInvalidos = array_diff($almacenIdsEnviados, $almacenesExistentesIds);
            if (!empty($idsInvalidos)) {
                return back()->withErrors(['almacen_stock' => 'Los almacenes con ID ' . implode(', ', $idsInvalidos) . ' no son válidos.'])->withInput();
            }
        }
         // Validar IDs de Proveedor
        $preciosInput = $validatedData['proveedor_precio'] ?? [];
        $proveedorIdsEnviados = array_keys($preciosInput);
         if (!empty($proveedorIdsEnviados)) {
             $proveedoresExistentesIds = Proveedores::whereIn('id', $proveedorIdsEnviados)->pluck('id')->toArray();
             $idsInvalidosProv = array_diff($proveedorIdsEnviados, $proveedoresExistentesIds);
             if (!empty($idsInvalidosProv)) {
                 return back()->withErrors(['proveedor_precio' => 'Los proveedores con ID ' . implode(', ', $idsInvalidosProv) . ' no son válidos.'])->withInput();
             }
         }

        // Creación y Registro de Movimientos
        try {
            DB::beginTransaction();

            $producto = Productos::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'precio' => $validatedData['precio'],
                'categoria_id' => $validatedData['categoria_id'],
            ]);

            $syncDataAlmacenes = [];
            // Array para almacenar los movimientos a registrar
            $movementsToRecord = [];

            foreach ($stockInput as $almacenId => $cantidad) {
                // Verificamos que es entero
                 $cantidad = (int)$cantidad;
                if ($cantidad >= 0) {
                    $syncDataAlmacenes[$almacenId] = ['cantidad' => $cantidad];

                    // Si hay stock inicial asignado (> 0), registrar movimiento de entrada
                    if ($cantidad > 0) {
                        $movementsToRecord[] = [
                            'producto_id' => $producto->id,
                            'almacen_id' => $almacenId,
                            'tipo' => 'entrada',
                            'cantidad' => $cantidad,
                            // O null si no hay usuario loggeado
                            'user_id' => Auth::id(),
                            'descripcion' => 'Stock inicial al crear producto',
                            // Registramos el timestamp del movimiento
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

             // Realizamos la sincronización de almacenes
             if (!empty($syncDataAlmacenes)) {
                 $producto->almacenes()->sync($syncDataAlmacenes);
             }

            // Registramos los movimientos de inventario
            if (!empty($movementsToRecord)) {
                // Usamos insert para mayor eficiencia
                MovimientosInventario::insert($movementsToRecord);
            }


            // Sync Proveedores (sin cambios)
             $syncDataProveedores = [];
             foreach ($preciosInput as $proveedorId => $precio) {
                 if (is_numeric($precio) && $precio !== null && $precio !== '') {
                     $syncDataProveedores[$proveedorId] = ['precio_proveedor' => $precio];
                 }
             }
             if (!empty($syncDataProveedores)) {
                 $producto->proveedores()->sync($syncDataProveedores);
             }

            DB::commit();

            return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error_inesperado' => 'Ocurrió un error al guardar el producto: ' . $e->getMessage()]);
        }
    }

    public function show(Productos $producto)
    {
        // Cargamos relaciones, incluyendo movimientos con almacén y usuario
        $producto->load(['categoria', 'almacenes', 'proveedores']);

        // Calculamos stock total
        $stockTotal = $producto->almacenes()->sum('productos_almacenes.cantidad');

        // Pasamos el producto (con proveedores cargados), stock total y movimientos a la vista
        return view('productos.show', compact('producto', 'stockTotal'));
    }


    public function edit(Productos $producto)
    {
        $producto->load(['almacenes', 'proveedores']);

        $categorias = Categorias::orderBy('nombre')->get();
        $almacenes = Almacenes::orderBy('nombre')->get();
        $proveedores = Proveedores::orderBy('nombre')->get();

        $almacenesActuales = $producto->almacenes->pluck('pivot.cantidad', 'id');
        $preciosProveedoresActuales = $producto->proveedores->pluck('pivot.precio_proveedor', 'id');

        return view('productos.edit', compact(
            'producto',
            'categorias',
            'almacenes',
            'almacenesActuales',
            'proveedores',
            'preciosProveedoresActuales'
        ));
    }

    public function update(Request $request, Productos $producto)
    {
         $validator = Validator::make($request->all(), [
             'nombre' => [
                 'required', 'string', 'max:255',
                 Rule::unique('productos', 'nombre')->ignore($producto->id),
             ],
             'descripcion' => 'nullable|string',
             'precio' => 'required|numeric|min:0',
             'categoria_id' => 'required|exists:categorias,id',

             'almacen_stock' => ['present', 'array'],
             'almacen_stock.*' => ['required', 'integer', 'min:0'],

             'proveedor_precio' => ['sometimes', 'array'],
             'proveedor_precio.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
         ], [
              'nombre.required' => 'El nombre del producto es obligatorio.',
              'nombre.unique' => 'Ya existe otro producto con este nombre.',
              'precio.required' => 'El precio de venta es obligatorio.',
              'precio.numeric' => 'El precio de venta debe ser un número.',
              'precio.min' => 'El precio de venta no puede ser negativo.',
              'categoria_id.required' => 'Debes seleccionar una categoría.',
              'categoria_id.exists' => 'La categoría seleccionada no es válida.',
              'almacen_stock.array' => 'Los datos de stock deben enviarse en el formato correcto.',
              'almacen_stock.*.required' => 'Se requiere una cantidad para cada almacén mostrado.',
              'almacen_stock.*.integer' => 'La cantidad de stock debe ser un número entero.',
              'almacen_stock.*.min' => 'La cantidad de stock no puede ser negativa.',
              'proveedor_precio.array' => 'Los datos de precios de proveedor deben enviarse en el formato correcto.',
              'proveedor_precio.*.numeric' => 'El precio del proveedor debe ser un número.',
              'proveedor_precio.*.min' => 'El precio del proveedor no puede ser negativo.',
              'proveedor_precio.*.max' => 'El precio del proveedor es demasiado alto.',
         ]);

         // Validación adicional: Precio de venta mayor que precio de proveedor
         $validator->after(function ($validator) {
             $validatedData = $validator->validated();
             $sellingPrice = $validatedData['precio'] ?? null;

             if (is_numeric($sellingPrice) && $sellingPrice >= 0) {
                 $supplierPrices = $validatedData['proveedor_precio'] ?? [];

                 foreach ($supplierPrices as $proveedorId => $precioProveedor) {
                      if (is_numeric($precioProveedor) && $precioProveedor !== null && $precioProveedor !== '') {
                         if ($sellingPrice <= $precioProveedor) {
                              $validator->errors()->add(
                                 'precio',
                                 "El precio de venta ({$sellingPrice}) debe ser mayor que el precio del proveedor con ID {$proveedorId} ({$precioProveedor})."
                              );
                         }
                      }
                 }
             }
         });


         if ($validator->fails()) {
             return back()->withErrors($validator)->withInput();
         }

         $validatedData = $validator->validated();

         // Validación Adicional (existencia de IDs)
         $stockInput = $validatedData['almacen_stock'] ?? [];
         $almacenIdsEnviados = array_keys($stockInput);
         if (!empty($almacenIdsEnviados)) {
             $almacenesExistentesIds = Almacenes::whereIn('id', $almacenIdsEnviados)->pluck('id')->toArray();
             $idsInvalidos = array_diff($almacenIdsEnviados, $almacenesExistentesIds);
             if (!empty($idsInvalidos)) {
                 return back()->withErrors(['almacen_stock' => 'Los almacenes con ID ' . implode(', ', $idsInvalidos) . ' no son válidos.'])->withInput();
             }
         }
          // Validar IDs de Proveedor
         $preciosInput = $validatedData['proveedor_precio'] ?? [];
         $proveedorIdsEnviados = array_keys($preciosInput);
          if (!empty($proveedorIdsEnviados)) {
              $proveedoresExistentesIds = Proveedores::whereIn('id', $proveedorIdsEnviados)->pluck('id')->toArray();
              $idsInvalidosProv = array_diff($proveedorIdsEnviados, $proveedoresExistentesIds);
              if (!empty($idsInvalidosProv)) {
                  return back()->withErrors(['proveedor_precio' => 'Los proveedores con ID ' . implode(', ', $idsInvalidosProv) . ' no son válidos.'])->withInput();
              }
          }


         // Ejecutamos la actualización y registrmaos los Movimientos
          try {
              DB::beginTransaction();

              // Obtenemos el stock actual antes de actualizar la tabla pivote
              $oldStock = $producto->almacenes()->pluck('cantidad', 'almacen_id')->toArray();
              $newStock = $validatedData['almacen_stock'] ?? [];
              // Array para almacenar los movimientos a registrar
              $movementsToRecord = [];
              // Obtenemos el ID del usuario loggeado
              $userId = Auth::id();

              // Actualizamos campos básicos del producto
              $producto->update([
                  'nombre' => $validatedData['nombre'],
                  'descripcion' => $validatedData['descripcion'],
                  'precio' => $validatedData['precio'],
                  'categoria_id' => $validatedData['categoria_id'],
              ]);

              // Preparamos los datos para la sincronización de almacenes
              $syncDataAlmacenes = [];
              $allAlmacenIds = array_unique(array_merge(array_keys($oldStock), array_keys($newStock))); // IDs de almacenes presentes antes o después

              foreach ($allAlmacenIds as $almacenId) {
                   $oldQty = $oldStock[$almacenId] ?? 0;
                   $newQty = $newStock[$almacenId] ?? 0;
                   $change = $newQty - $oldQty;

                   // Preparamos los datos para el sync (si la nueva cantidad es >= 0)
                   //Si la nueva cantidad es 0 y antes había stock, sync lo eminará.
                   if ($newQty >= 0) {
                        $syncDataAlmacenes[$almacenId] = ['cantidad' => $newQty];
                   }


                   // Registrar movimiento SOLO si hay un cambio en la cantidad
                   if ($change != 0) {
                       $movementsToRecord[] = [
                           'producto_id' => $producto->id,
                           'almacen_id' => $almacenId,
                           'tipo' => $change > 0 ? 'entrada' : 'salida',
                           // La cantidad del movimiento siempre es positiva
                           'cantidad' => abs($change),
                           'user_id' => $userId,
                           'descripcion' => 'Ajuste por edición de producto',
                           'created_at' => now(),
                           'updated_at' => now(),
                       ];
                   }
              }

              // Realizar la sincronización de almacenes
              $producto->almacenes()->sync($syncDataAlmacenes);

              // Registrar los movimientos de inventario
              if (!empty($movementsToRecord)) {
                 // Usamos insert para mayor eficiencia
                  MovimientosInventario::insert($movementsToRecord);
              }

              // Sync Proveedores (sin cambios)
               $syncDataProveedores = [];
               foreach ($preciosInput as $proveedorId => $precio) {
                   if (is_numeric($precio) && $precio !== null && $precio !== '') {
                       $syncDataProveedores[$proveedorId] = ['precio_proveedor' => $precio];
                   }
               }
                $producto->proveedores()->sync($syncDataProveedores);


              DB::commit();

               // Redirigimos a la vista de detalles del producto actualizado
              return redirect()->route('productos.show', $producto->id)->with('success', 'Producto actualizado correctamente.');

          } catch (\Exception $e) {
              DB::rollBack();
              return back()->withInput()->withErrors(['error_inesperado' => 'Ocurrió un error al actualizar el producto: ' . $e->getMessage()]);
          }
    }

    public function destroy(Productos $producto)
    {
        try {
            DB::beginTransaction();

             // Obtenemos el stock actual antes de eliminar
             $stockAntesEliminar = $producto->almacenes()->pluck('productos_almacenes.cantidad', 'almacenes.id')->toArray(); // Pluck por almacen_id

             // Registramos movimientos de salida para todo el stock existente
             $movementsToRecord = [];
             $userId = Auth::id();

             foreach($stockAntesEliminar as $almacenId => $cantidad) {
                 if ($cantidad > 0) {
                      $movementsToRecord[] = [
                           'producto_id' => $producto->id,
                           'almacen_id' => $almacenId,
                           'tipo' => 'salida',
                           'cantidad' => $cantidad,
                           'user_id' => $userId,
                           'descripcion' => 'Eliminación de producto',
                            'created_at' => now(),
                            'updated_at' => now(),
                       ];
                 }
             }

            // Registramos los movimientos de inventario ANTES de eliminar las relaciones
            if (!empty($movementsToRecord)) {
                MovimientosInventario::insert($movementsToRecord);
            }

            // Desvinculamos proveedores
            $producto->proveedores()->detach();
            // Desvinculamos almacenes, con esto eliminamos las filas de la tabla pivote productos_almacenes
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

     public function indexByAlmacen(Almacenes $almacen)
    {
        // Obtenemos el usuario autenticado
        $user = Auth::user();

        // Si el usuario NO es administrador Y su almacen_id asignado es diferente al ID del almacén que intenta ver
        if (!$user->is_admin && $user->almacen_id !== $almacen->id) {
            // Denegamos el acceso
            abort(403, 'No tienes permiso para ver los productos de este almacén.');
        }
        // Cargamos las relaciones
        $productos = $almacen->productos()
                             ->with(['categoria', 'proveedores'])
                             ->wherePivot('cantidad', '>', 0)
                             ->orderBy('productos.nombre', 'asc')
                             ->paginate(15);

        // Calculamos stock total para cada producto
         $productos->each(function($producto) use ($almacen) {
              // Cargamos la relación pivot para este almacén si no está ya cargada
             $almacenPivot = $producto->almacenes->where('id', $almacen->id)->first();
             $producto->stock_en_este_almacen = $almacenPivot ? $almacenPivot->pivot->cantidad : 0;
         });


        return view('productos.index-by-almacen', compact('productos', 'almacen'));
    }

      public function movimientos(Productos $producto)
      {
        // Cargamos la relación movimientos y sus relaciones anidadas
          $producto->load(['movimientos.almacen', 'movimientos.user']);
          // Redirigimos a la sección de movimientos
          return redirect()->route('productos.show', $producto->id . '#movimientos');
      }

}
