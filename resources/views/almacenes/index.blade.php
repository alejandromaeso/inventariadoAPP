@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Almacenes</h1>

    <!-- Botón para agregar un nuevo almacén -->
    <a href="{{ route('almacenes.create') }}" class="btn btn-primary mb-3">Añadir Almacén</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($almacenes as $almacen)
            <tr>
                <td>{{ $almacen->id }}</td>
                <td>{{ $almacen->nombre }}</td>
                <td>{{ $almacen->ubicacion }}</td>
                <td>
                    <a href="{{ route('productos.show', $almacen->id) }}" class="btn btn-info">Ver Productos</a>
                    <a href="{{ route('almacenes.edit', $almacen->id) }}" class="btn btn-warning">Editar Almacén</a>
                    <form action="{{ route('almacenes.destroy', $almacen->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este almacén?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
