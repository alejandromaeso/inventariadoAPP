@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista de Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">Añadir Producto</a>
    </div>

    {{-- Mostrar mensajes de éxito/error --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
     @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock Total</th>
                    <th>Categoría</th>
                    <th>Almacenes</th>
                    <th style="width: 250px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ number_format($producto->precio, 2, ',', '.') }} €</td>
                    <td>
                        <span class="badge fs-6 {{ ($producto->stock_total ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                             {{ $producto->stock_total ?? 0 }}
                        </span>
                    </td>
                    <td>
                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                    </td>
                    <td>
                        {{-- Mostrar solo nombres o cantidad de almacenes para brevedad --}}
                        @forelse($producto->almacenes as $almacen)
                            <span class="badge bg-secondary">{{ $almacen->nombre }}</span>
                        @empty
                            <span class="badge bg-light text-dark">Ninguno</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este producto?\nEsta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay productos registrados todavía.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $productos->links() }}
    </div>

</div>
@endsection

