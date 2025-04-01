@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Producto</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h3>{{ $producto->nombre }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Descripción:</strong> {{ $producto->descripcion ?? 'N/A' }}</p>
            <p><strong>Precio:</strong> {{ number_format($producto->precio, 2, ',', '.') }} €</p>
            <p><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
            <p><strong>Stock Total (todos los almacenes):</strong>
                 {{ $producto->almacenes()->sum('productos_almacenes.cantidad') }}
            </p>

        </div>
    </div>

    <div class="card">
         <div class="card-header">
            <h4>Stock por Almacén</h4>
        </div>
        <div class="card-body">
             @forelse($producto->almacenes as $almacen)
                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <span>
                        <i class="fas fa-warehouse me-2"></i>
                        {{ $almacen->nombre }} ({{ $almacen->ubicacion ?? 'Ubicación no especificada' }})
                    </span>
                    <span class="badge bg-primary rounded-pill fs-6">
                        {{ $almacen->pivot->cantidad }} unidades
                    </span>
                </div>
             @empty
                <p class="text-muted">Este producto no tiene stock asignado en ningún almacén.</p>
             @endforelse
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver a la lista</a>
        <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning">Editar Producto</a>
    </div>
</div>

@endsection
