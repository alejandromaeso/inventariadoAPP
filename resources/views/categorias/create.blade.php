@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Añadir Nueva Categoria</h1>

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Guardar Categoria</button>
    </form>
</div>
@endsection
