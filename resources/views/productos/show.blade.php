@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Verifica si la variable $producto existe antes de usarla --}}
    @if(isset($producto))
        <h1>Detalles del Producto</h1>

        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">{{ $producto->nombre }}</h3>
                <span class="badge bg-info text-dark">ID: {{ $producto->id }}</span>
            </div>
            <div class="card-body">
                {{-- Usamos nl2br para respetar saltos de línea en descripción, esto convierte los saltos de línea (\n) --}}
                {{-- En etiquetas HTML <br> --}}
                <p><strong>Descripción:</strong> {!! nl2br(e($producto->descripcion ?? 'N/A')) !!}</p>
                <p><strong>Precio Venta:</strong> {{ number_format($producto->precio, 2, ',', '.') }} €</p>
                {{-- Comprobar si la categoría existe antes de acceder a su nombre --}}
                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
                <p><strong>Stock Total (todos los almacenes):</strong>
                    {{-- Usamos la variable $stockTotal pasada por el controlador --}}
                    <span class="badge fs-6 {{ ($stockTotal ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $stockTotal ?? 0 }}
                    </span>
                </p>
            </div>
            <div class="card-footer text-muted">
                Creado: {{ $producto->created_at->format('d/m/Y H:i') }} |
                Actualizado: {{ $producto->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>

        {{-- Card para Stock por Almacén --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4>Stock por Almacén</h4>
            </div>
            <div class="card-body">
                @forelse($producto->almacenes as $almacen)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 @unless($loop->last) border-bottom @endunless"> {{-- Añadido border-bottom condicional --}}
                        <span>
                             <i class="fas fa-warehouse me-2 text-secondary"></i> {{ $almacen->nombre }}
                             <small class="text-muted">({{ $almacen->ubicacion ?? 'Sin ubicación' }})</small>
                        </span>
                        {{-- Mostrar cantidad 0 con color diferente --}}
                        <span class="badge rounded-pill fs-6 {{ ($almacen->pivot->cantidad ?? 0) > 0 ? 'bg-primary' : 'bg-warning text-dark' }}">
                             {{ $almacen->pivot->cantidad ?? 0 }} unidades
                        </span>
                    </div>
                @empty
                     <p class="text-muted">Este producto no tiene stock asignado en ningún almacén.</p>
                @endforelse
            </div>
        </div>

        {{-- Proveedores Asociados y Precios de Compra    --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4>Proveedores Asociados y Precios de Compra</h4>
            </div>
            <div class="card-body">
                 @forelse($producto->proveedores as $proveedor)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 @unless($loop->last) border-bottom @endunless">
                         <span class="col-md-6">
                              <i class="fas fa-user-tie me-2 text-secondary"></i> {{ $proveedor->nombre }}
                         </span>
                         <span class="col-md-6 text-end">
                             <strong>Precio Compra:</strong>
                             {{-- Accedemos al precio desde la tabla pivote y lo formateamos --}}
                             <span class="badge bg-success fs-6">
                                  {{ number_format($proveedor->pivot->precio_proveedor ?? 0, 2, ',', '.') }} €
                             </span>
                         </span>
                    </div>
                 @empty
                     <p class="text-muted">Este producto no tiene proveedores asociados.</p>
                 @endforelse
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('productos.index') }}" class="btn btn-secondary me-2">
                 <i class="fas fa-list me-1"></i> Volver a la lista
            </a>
            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning me-2">
                  <i class="fas fa-edit me-1"></i> Editar Producto
            </a>
            {{-- Botón eliminar --}}
            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar \'{{ $producto->nombre }}\'?\nEsta acción no se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Eliminar Producto">
                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                </button>
            </form>
        </div>
    @else
        {{-- Mensaje si $producto no existe por alguna razón --}}
        <div class="alert alert-danger">Producto no encontrado.</div>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver a la lista</a>
    @endif
</div>
@endsection
