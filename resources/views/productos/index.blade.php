@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista de Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-primary" title="Añadir nuevo producto">
             <i class="fas fa-plus me-1"></i> Añadir Producto
        </a>
    </div>

    {{-- Mensajes flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio Venta</th>
                            <th>Stock Total</th>
                            <th>Categoría</th>
                            <th>Almacenes (Stock)</th>
                            <th>Proveedores (Precio Compra)</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ number_format($producto->precio, 2, ',', '.') }} €</td>
                            <td>
                                @php
                                    $stockToShow = $producto->stock_total ?? 0;
                                    $stockClass = $stockToShow > 0 ? 'bg-success' : 'bg-danger';
                                @endphp
                                <span class="badge fs-6 {{ $stockClass }}">
                                    {{ $stockToShow }}
                                </span>
                            </td>
                            <td>
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </td>
                            <td>
                                @php
                                    // Filtro para mostrar solo almacenes con stock > 0
                                    $almacenesConStock = $producto->almacenes->filter(function ($almacen) {
                                        return isset($almacen->pivot->cantidad) && $almacen->pivot->cantidad > 0;
                                    });
                                @endphp
                                @forelse($almacenesConStock as $almacen)
                                    <span class="badge bg-success me-1"
                                          title="{{ $almacen->nombre }} (Cantidad: {{ $almacen->pivot->cantidad }})">
                                        {{ $almacen->nombre }}
                                    </span>
                                @empty
                                    <span class="badge bg-light text-dark">Ninguno</span>
                                @endforelse
                            </td>
                             {{-- CELDA PARA MOSTRAR PROVEEDORES Y PRECIOS --}}
                            <td>
                                {{-- Iteramos sobre los proveedores cargados para este producto --}}
                                @forelse ($producto->proveedores as $proveedor)
                                     {{-- Mostramos un badge por cada proveedor asociado --}}
                                    <span class="badge bg-info text-dark me-1 mb-1" title="Precio de compra con {{ $proveedor->nombre }}">
                                         <i class="fas fa-user-tie small me-1"></i>
                                         {{ $proveedor->nombre }}:
                                         {{-- Mostramos el precio desde la tabla pivote, con formato --}}
                                         {{ number_format($proveedor->pivot->precio_proveedor ?? 0, 2, ',', '.') }} €
                                     </span>
                                @empty
                                    {{-- Mensaje si no hay proveedores asociados --}}
                                    <span class="badge bg-light text-dark">Ninguno</span>
                                @endforelse
                            </td>
                             {{-- ============================================= --}}
                            <td class="text-end">
                                <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-warning" title="Editar Producto"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar \'{{ $producto->nombre }}\'?\nEsta acción no se puede deshacer y eliminará su stock y asociaciones.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Producto"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                No hay productos registrados todavía.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Paginación --}}
        @if ($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
            <div class="card-footer d-flex justify-content-center">
                 {{ $productos->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
