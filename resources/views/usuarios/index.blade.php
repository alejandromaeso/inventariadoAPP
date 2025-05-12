@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')

    <h1>Gestión de Usuarios</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('register') }}" class="btn btn-primary">Crear Nuevo Usuario</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Es Administrador</th>
                <th>Almacén</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->is_admin ? 'Sí' : 'No' }}</td>
                      {{-- Mostramos el nombre del almacén si existe, de lo contrario, indicar "Todos" o "Sin asignar" --}}
                    <td>
                        @if ($user->isAdmin())
                            Todos (Admin)
                        @else
                        {{-- Accedemos a la relación; ?? '' indica si almacen es nulo --}}
                            {{ $user->almacen->nombre ?? 'Sin asignar' }}
                        @endif
                    </td>
                    <td>
                      {{-- Botón/Enlace de Editar --}}
                      <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-sm btn-warning me-2">Editar</a>

                      {{-- Botón de Eliminar --}}
                      {{-- Usamos un formulario para enviar una petición DELETE --}}
                      <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar a este usuario?')">
                              Eliminar
                          </button>
                      </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
         {{ $users->links() }}
    </div>

@endsection
