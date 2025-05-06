@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Historial de Movimientos de Inventario</h1>
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
    {{-- Fin mensajes flash --}}

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha/Hora</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th>Tipo</th>
                            <th class="text-end">Cantidad</th>
                            <th>Usuario</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Iteramos sobre la colección de movimientos paginados --}}
                        @forelse ($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            {{-- Formateamos la fecha --}}
                            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- Accedemos al nombre del producto a través de la relación --}}
                                {{ $movimiento->producto->nombre ?? 'Producto Desconocido' }}
                            </td>
                            <td>
                                {{-- Accedemos al nombre del almacén a través de la relación --}}
                                {{ $movimiento->almacen->nombre ?? 'Almacén Desconocido' }}
                            </td>
                            <td>
                                {{-- Mostramos el tipo con un badge visual --}}
                                @php
                                     $tipoClass = $movimiento->tipo === 'entrada' ? 'bg-success' : 'bg-danger';
                                     $tipoText = $movimiento->tipo === 'entrada' ? 'Entrada' : 'Salida';
                                @endphp
                                <span class="badge {{ $tipoClass }}">{{ $tipoText }}</span>
                            </td>
                            {{-- Mostramos la cantidad --}}
                            <td class="text-end">{{ $movimiento->cantidad }}</td>
                            <td>
                                {{-- Accedemos al nombre del usuario a través de la relación --}}
                                {{ $movimiento->user->name ?? 'Sistema' }}
                            </td>
                            <td>
                                {{-- Mostramos la descripción --}}
                                {{ $movimiento->descripcion ?? 'Sin descripción' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- Colspan debe coincidir con el número de columnas (8 en este caso) --}}
                            <td colspan="8" class="text-center text-muted py-3">
                                <i class="fas fa-exchange-alt fa-2x mb-2"></i><br>
                                No hay movimientos de inventario registrados todavía.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        @if ($movimientos instanceof \Illuminate\Pagination\LengthAwarePaginator && $movimientos->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $movimientos->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

