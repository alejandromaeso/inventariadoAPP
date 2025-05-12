@extends('layouts.app')
{{-- Título dinámico --}}
@section('title', 'Editar Usuario: ' . $user->name)
@section('content')

    <h1>Editar Usuario</h1>

    {{-- Formulario de edición --}}
    {{-- El action apunta a la ruta de actualización y le pasa el usuario --}}
    {{-- Method es POST, pero usamos @method('PATCH') para la petición PATCH real --}}
    <form action="{{ route('usuarios.update', $user) }}" method="POST">
        @csrf
        {{-- Simulamos una petición PATCH --}}
        @method('PATCH')

        {{-- Campo Nombre --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            {{-- Usamos old() para mantener el valor si hay error de validación --}}
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
             {{-- Usamos old() para mantener el valor si hay error de validación --}}
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
             @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
             @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Confirmar Contraseña (solo si se ingresa nueva contraseña) --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

         {{-- Campo Es Administrador (Checkbox) --}}
         <div class="mb-3 form-check">
            {{-- Usamos old() y el valor actual del usuario para marcar el checkbox --}}
            <input type="checkbox" class="form-check-input @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_admin">Es Administrador</label>
             @error('is_admin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="almacen_id" class="form-label">Almacén</label>
            <select class="form-select @error('almacen_id') is-invalid @enderror" id="almacen_id" name="almacen_id">
                <option value="">-- Seleccionar Almacén --</option>
                @foreach ($almacenes as $almacen)
                    {{-- old() para mantener la selección si hay error --}}
                    {{-- $user->almacen_id == $almacen->id para seleccionar el almacén actual del usuario --}}
                    <option value="{{ $almacen->id }}" {{ old('almacen_id', $user->almacen_id) == $almacen->id ? 'selected' : '' }}>
                        {{ $almacen->nombre }}
                    </option>
                @endforeach
            </select>
            @error('almacen_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botón de guardar --}}
        <button type="submit" class="btn btn-success">Actualizar Usuario</button>

        {{-- Enlace para volver a la lista --}}
        <a href="{{ route('usuarios') }}" class="btn btn-secondary">Cancelar</a> {{-- Enlace de vuelta al índice --}}

    </form>

@endsection
