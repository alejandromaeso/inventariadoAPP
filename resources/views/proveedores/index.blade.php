@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista de Proveedores</h1>
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary" title="Añadir nuevo proveedor">
            <i class="fas fa-plus me-1"></i> Añadir Proveedor
        </a>
    </div>

    {{-- Mostrar mensajes flash --}}
    @include('partials.flash-messages')

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th class="text-center">Nº Productos</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->id }}</td>
                            {{-- Nombre usa el getter para mostrar ucfirst (la primera en mayúscula) --}}
                            <td>{{ $proveedor->nombre }}</td>
                            <td>{{ $proveedor->telefono ?? '-' }}</td>
                            <td>{{ $proveedor->email ?? '-' }}</td>
                             {{-- Dirección usa el getter para mostrar ucfirst (la primera en mayúscula) --}}
                            <td>{{ $proveedor->direccion ?? '-' }}</td>
                            <td class="text-center">
                                {{-- Mostramos el número de productos que tenemos sin cargarlos todos--}}
                                {{-- Para que $proveedor->productos->count() no cause N+1 --}}
                                {{ $proveedor->productos_count ?? $proveedor->contarProductos() }}
                            </td>
                            <td class="text-end">

                                {{-- Botón Editar --}}
                                <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-sm btn-warning" title="Editar Proveedor"><i class="fas fa-edit"></i></a>

                                {{-- Botón Eliminar --}}
                                <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar al proveedor \'{{ $proveedor->nombre }}\'?\nEsta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Proveedor"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- Ajustamos el colspan al número de columnas --}}
                            <td colspan="7" class="text-center text-muted py-3">
                                <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                                No hay proveedores registrados todavía.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        @if ($proveedores instanceof \Illuminate\Pagination\LengthAwarePaginator && $proveedores->hasPages())
            <div class="card-footer d-flex justify-content-center">
                 {{ $proveedores->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
