@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Añadir Nuevo Producto</h1>

    {{-- Mostramos errores generales de validación y errores específicos --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    {{-- Evitamos duplicar mensajes de error que se mostrarán --}}
                    @if (!Str::startsWith($error, ['El nombre', 'El precio', 'Debes seleccionar una categoría', 'La categoría', 'Los datos de stock', 'Se requiere una cantidad', 'La cantidad', 'Los datos de precios', 'El precio del proveedor']))
                        <li>{{ $error }}</li>
                    @endif
                @endforeach
                {{-- Mostramos errores específicos de arrays que no son por elemento individual --}}
                @if ($errors->has('almacen_stock') && !$errors->has('almacen_stock.*') && !Str::contains($errors->first('almacen_stock'), 'ID'))
                     <li>{{ $errors->first('almacen_stock') }}</li>
                @endif
                @if ($errors->has('proveedor_precio') && !$errors->has('proveedor_precio.*') && !Str::contains($errors->first('proveedor_precio'), 'ID'))
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

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        {{-- Card para Datos Básicos del Producto --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Datos Básicos del Producto</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio Venta <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('precio') is-invalid @enderror" id="precio" name="precio" value="{{ old('precio') }}" required min="0" step="0.01" placeholder="Precio de venta al público">
                    @error('precio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                     <small class="form-text text-muted">Precio de venta final del producto.</small>
                </div>

                {{-- Campo para seleccionar la categoría --}}
                <div class="mb-3">
                    <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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

        {{-- Card para Stock Inicial por Almacén --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Stock Inicial por Almacén</div>
            <div class="card-body">
                {{-- Muestra errores generales relacionados con almacen_stock si existen y no son por ID inválido --}}
                @error('almacen_stock')
                     @if (!Str::contains($message, 'ID'))
                       <div class="text-danger d-block mb-2 small">{{ $message }}</div>
                     @endif
                @enderror

                <div class="border p-3 rounded mb-3">
                    @forelse($almacenes as $almacen)
                        <div class="row align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="form-check-label" for="almacen_stock_{{ $almacen->id }}">
                                     <i class="fas fa-warehouse me-1 text-secondary"></i> {{ $almacen->nombre }}
                                </label>
                            </div>
                             <div class="col-md-4">
                                <input type="number"
                                       name="almacen_stock[{{ $almacen->id }}]"
                                       id="almacen_stock_{{ $almacen->id }}"
                                       value="{{ old('almacen_stock.' . $almacen->id, 0) }}"
                                       min="0"
                                       class="form-control form-control-sm @error('almacen_stock.' . $almacen->id) is-invalid @enderror"
                                       aria-label="Cantidad para {{ $almacen->nombre }}"
                                       placeholder="Cantidad">
                                @error('almacen_stock.' . $almacen->id)
                                   <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                             </div>
                        </div>
                    @empty
                        <p class="text-muted">No hay almacenes disponibles para asignar.</p>
                    @endforelse
                </div>
                 <small class="form-text text-muted">
                     Indica la cantidad inicial (puede ser 0) para cada almacén.
                 </small>
            </div>
        </div>

        {{-- Asignar Proveedores y Precios de Compra --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Proveedores y Precios de Compra</div>
            <div class="card-body">

                 {{-- Muestra errores generales relacionados con proveedor_precio si existen y no son por ID inválido --}}
                 @error('proveedor_precio')
                     @if (!Str::contains($message, 'ID'))
                       <div class="text-danger d-block mb-2 small">{{ $message }}</div>
                     @endif
                 @enderror

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
                                            {{-- Recuperamos valor anterior si falla la validación --}}
                                            value="{{ old('proveedor_precio.' . $proveedor->id) }}"
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
                 <small class="form-text text-muted">
                     Introduce el precio de compra (€) **sólo** para los proveedores que suministran este producto. Deja vacío los demás.
                 </small>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <a href="{{ route('productos.index') }}" class="btn btn-secondary me-md-2">
                 <i class="fas fa-times me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Producto
            </button>
        </div>
    </form>
</div>
@endsection

