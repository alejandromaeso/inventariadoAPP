@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- Título específico para el almacén --}}
            <h1>Productos en Almacén: <span class="text-primary">{{ $almacen->nombre }}</span></h1>
        </div>

        {{-- Mensajes flash --}}
        @include('partials.flash-messages')

        <div class="card shadow-sm">
            <div class="card-header">
                <p class="mb-0">Mostrando productos con existencia en <strong>{{ $almacen->nombre }}</strong>
                    ({{ $almacen->ubicacion ?? 'Sin ubicación' }})</p>
            </div>
            <div class="card-body">
                @if ($productos->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Prod.</th>
                                    <th>Nombre Producto</th>
                                    <th>Categoría</th>
                                    <th>Precio Unit.</th>
                                    {{-- Columna específica para la cantidad en ESTE almacén --}}
                                    <th class="text-center">Cantidad en este Almacén</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Iteramos sobre los productos filtrados --}}
                                @foreach ($productos as $producto)
                                    <tr>
                                        <td>{{ $producto->id }}</td>
                                        <td>{{ $producto->nombre }}</td>
                                        <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                                        <td>{{ number_format($producto->precio, 2, ',', '.') }} €</td>
                                        <td class="text-center">
                                            {{-- Mostramos la cantidad del PIVOT que corresponde a la relación entre $almacen y $producto --}}
                                            <span
                                                class="badge fs-6 rounded-pill {{ ($producto->pivot->cantidad ?? 0) > 0 ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                {{ $producto->pivot->cantidad ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {{-- Las siguientes acciones siguen operando sobre el producto --}}
                                            <a href="{{ route('productos.show', $producto->id) }}"
                                                class="btn btn-sm btn-info" title="Ver Detalles del Producto"><i
                                                    class="fas fa-eye"></i></a>
                                            <a href="{{ route('productos.edit', $producto->id) }}"
                                                class="btn btn-sm btn-warning" title="Editar Producto"><i
                                                    class="fas fa-edit"></i></a>
                                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de eliminar \'{{ $producto->nombre }}\'?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Eliminar Producto"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No se encontraron productos asignados a este almacén.</p>
                @endif
            </div>

            {{-- Paginación --}}
            @if ($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-list me-1"></i> Ver Todos los Productos
            </a>

            {{-- Enlace para volver a la lista de almacenes (solo para admin) --}}
            @auth
                @php $user = Auth::user(); @endphp
                @if ($user->isAdmin() && Route::has('almacenes.index'))
                    <a href="{{ route('almacenes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-warehouse me-1"></i> Ver Todos los Almacenes
                    </a>
                @endif
            @endauth
        </div>

    @endsection
