@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Añadir Nuevo Almacén</h1>

    <form action="{{ route('almacenes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación</label>
            <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Guardar Almacén</button>
    </form>
</div>
@endsection
