@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Producto: {{ $producto->nombre }}</h1>

    {{-- Mostrar errores generales de validación y errores específicos --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Errores de validación:</h6>
            <ul>
                @foreach ($errors->all() as $error)
                    {{-- Evita duplicar mensajes de error que se mostrarán inline o ya se muestran de forma general --}}
                    @if (!Str::startsWith($error, ['El nombre', 'El precio de venta', 'Debes seleccionar una categoría', 'La categoría', 'Los datos de stock', 'Se requiere una cantidad', 'La cantidad', 'Los datos de precios', 'El precio del proveedor', 'Los almacenes con ID', 'Los proveedores con ID']))
                        <li>{{ $error }}</li>
                    @endif
                @endforeach
                 {{-- Mostrar errores de arrays que no son por elemento individual o IDs inválidos --}}
                 @if ($errors->has('almacen_stock') && !Str::contains($errors->first('almacen_stock'), ['cantidad', 'ID']))
                      <li>{{ $errors->first('almacen_stock') }}</li>
                 @endif
                 @if ($errors->has('proveedor_precio') && !Str::contains($errors->first('proveedor_precio'), ['numérico', 'ID']))
                       <li>{{ $errors->first('proveedor_precio') }}</li>
                 @endif
                  {{-- Mensaje de error si se enviaron IDs inválidos de Almacén --}}
                  @if ($errors->has('almacen_stock') && Str::contains($errors->first('almacen_stock'), 'no son válidos'))
                      <li>{{ $errors->first('almacen_stock') }}</li>
                  @endif
                  {{-- Mensaje de error si se enviaron IDs inválidos de Proveedor --}}
                  @if ($errors->has('proveedor_precio') && Str::contains($errors->first('proveedor_precio'), 'no son válidos'))
                      <li>{{ $errors->first('proveedor_precio') }}</li>
                  @endif
                 {{-- Mensaje de error inesperado --}}
                 @if ($errors->has('error_inesperado'))
                      <li>{{ $errors->first('error_inesperado') }}</li>
                 @endif
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Card para Datos Básicos del Producto --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Datos Básicos del Producto</div>
            <div class="card-body">
                {{-- Campos estándar: Nombre, Descripción --}}
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre) }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                     @error('descripcion')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="precio" class="form-label">Precio Venta (€) <span class="text-danger">*</span></label>
                        {{-- El error de precio vs proveedor precio se mostrará aquí --}}
                        <input type="number" step="0.01" min="0" name="precio" id="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $producto->precio) }}" required placeholder="Precio de venta al público">
                        @error('precio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                         <small class="form-text text-muted">Precio de venta final del producto.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                <option value="">Selecciona una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                         @error('categoria_id')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Card para Stock por Almacén --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Stock por Almacén</div>
             <div class="card-body">
                  {{-- Mensaje de error general para stock si existe y no es por cantidad individual o ID --}}
                  @error('almacen_stock')
                       @if (!Str::contains($message, ['cantidad', 'ID']))
                        <div class="text-danger d-block mb-2 small">{{ $message }}</div>
                       @endif
                  @enderror

                  @if($almacenes->isEmpty())
                       <p class="text-warning">No hay almacenes registrados en el sistema. Debes crear almacenes primero.</p>
                  @else
                       <div class="border p-3 rounded mb-3">
                           @foreach($almacenes as $almacen)
                               <div class="row mb-3 align-items-center border-bottom pb-3">
                                   <div class="col-md-6">
                                       <label for="almacen_stock_{{ $almacen->id }}" class="form-label mb-0">
                                            <i class="fas fa-warehouse me-2"></i>
                                            {{ $almacen->nombre }}
                                            <small class="text-muted">({{ $almacen->ubicacion ?? 'Sin ubicación' }})</small>
                                       </label>
                                   </div>
                                   <div class="col-md-4">
                                        <input
                                            type="number"
                                            name="almacen_stock[{{ $almacen->id }}]"
                                            id="almacen_stock_{{ $almacen->id }}"
                                            class="form-control form-control-sm @error('almacen_stock.' . $almacen->id) is-invalid @enderror"
                                            min="0"
                                            step="1"
                                            {{-- Usamos old() con notación de punto y fallback al stock actual --}}
                                            value="{{ old('almacen_stock.' . $almacen->id, $almacenesActuales->get($almacen->id, 0)) }}"
                                            placeholder="Cantidad"
                                        >
                                        {{-- Mensaje de error específico para este input --}}
                                        @error('almacen_stock.' . $almacen->id)
                                            <div class="invalid-feedback d-block">
                                                 {{ $message }}
                                            </div>
                                        @enderror
                                   </div>
                               </div>
                           @endforeach
                       </div>
                  @endif
                   <small class="form-text text-muted">
                       Indica la cantidad actual de este producto en cada almacén.
                   </small>
             </div>
         </div>

        {{-- Asignar Proveedores y Precios de Compra --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Proveedores y Precios de Compra</div>
            <div class="card-body">

                {{-- Muestra errores generales relacionados con proveedor_precio si existen y no son por elemento individual o ID --}}
                @error('proveedor_precio')
                    @if (!Str::contains($message, ['numérico', 'ID']))
                        <div class="text-danger d-block mb-2 small">{{ $message }}</div>
                    @endif
                @enderror

                @if($proveedores->isEmpty())
                    <p class="text-warning">No hay proveedores registrados en el sistema.</p>
                @else
                     {{-- Preparar datos de precios actuales para fácil acceso (ya lo hace el controller) --}}
                     {{-- $preciosProveedoresActuales = $producto->proveedores->pluck('pivot.precio_proveedor', 'id'); --}}
                    <div class="border p-3 rounded mb-3">
                        @forelse($proveedores as $proveedor)
                            <div class="row align-items-center mb-2">
                                <div class="col-md-6">
                                    <label for="proveedor_precio_{{ $proveedor->id }}" class="form-label mb-0">
                                        <i class="fas fa-user-tie me-1 text-secondary"></i> {{ $proveedor->nombre }}
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    {{-- Input para el precio específico de este proveedor --}}
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">€</span>
                                        <input type="number"
                                                {{-- Nombre del array esperado por el controlador --}}
                                                name="proveedor_precio[{{ $proveedor->id }}]"
                                                id="proveedor_precio_{{ $proveedor->id }}"
                                                {{-- Recuperar valor anterior si falla validación, O el precio actual del proveedor --}}
                                                value="{{ old('proveedor_precio.' . $proveedor->id, $preciosProveedoresActuales->get($proveedor->id)) }}"
                                                min="0"
                                                step="0.01"
                                                {{-- Clase de error específica para este input --}}
                                                class="form-control @error('proveedor_precio.' . $proveedor->id) is-invalid @enderror"
                                                placeholder="Precio Compra"
                                                aria-label="Precio de compra para {{ $proveedor->nombre }}">
                                    </div>
                                    {{-- Mensaje de error específico para este input --}}
                                    @error('proveedor_precio.' . $proveedor->id)
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No hay proveedores registrados en el sistema.</p>
                        @endforelse
                    </div>
                @endif
                <small class="form-text text-muted">
                     Introduce el precio de compra (€) **sólo** para los proveedores que suministran este producto. Deja vacío los demás.
                </small>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
             <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-secondary me-md-2">
                  <i class="fas fa-times me-1"></i> Cancelar y Volver
             </a>
             <button type="submit" class="btn btn-success">
                 <i class="fas fa-save me-1"></i> Actualizar Producto
             </button>
        </div>
    </form>
</div>
@endsection

{{-- CSS para mejorar alineación --}}
@push('styles')
<style>
    /* Ajuste para que el feedback de validación de inputs con input-group se muestre correctamente */
    .input-group .invalid-feedback {
        /* Aseguramos que ocupe el ancho completo de la columna si es necesario */
        width: 100%;
        /* Espaciamos encima */
        margin-top: .25rem;
    }
     /* Fijamos que el invalid-feedback debajo del input-group sea visible */
    .invalid-feedback.d-block {
        display: block !important;
    }
</style>
@endpush
