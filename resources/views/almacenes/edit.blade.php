@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Almacén</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('almacenes.update', $almacen->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Almacén</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $almacen->nombre) }}" required>
        </div>

        <div class="mb-3">
            <label for="ubicacion" class="form-label">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion', $almacen->ubicacion) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Almacén</button>
        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
