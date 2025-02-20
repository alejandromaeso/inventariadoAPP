@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Almacén</h1>

    <ul class="list-group">
        <li class="list-group-item"><strong>ID:</strong> {{ $almacenes->id }}</li>
        <li class="list-group-item"><strong>Nombre:</strong> {{ $almacenes->nombre }}</li>
        <li class="list-group-item"><strong>Ubicación:</strong> {{ $almacenes->ubicacion }}</li>
    </ul>

    <a href="{{ route('almacenes.index') }}" class="btn btn-primary mt-3">Volver a la lista</a>
</div>
@endsection
