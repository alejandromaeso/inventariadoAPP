@extends('layouts.app')

<style>
    /* Agregar sombra a los botones */
.btn-lg {
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Sombra para la tabla */
.table-striped {
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Agregar estilo al título */
h1.display-4 {
    font-size: 3rem;
    font-weight: 700;
    color: #2c3e50;
}

h2 {
    font-size: 2rem;
    font-weight: 600;
}

h3 {
    font-size: 1.5rem;
    font-weight: 600;
}

</style>

@section('content')
<div class="container">
    <!-- Sección de bienvenida -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="display-4">Bienvenido a la Aplicación de Inventariado AMC</h1>
            <p class="lead">Gestiona fácilmente tus almacenes, productos, movimientos de inventario, categorías y proveedores.</p>
        </div>
    </div>

    <!-- Botones de acceso directo -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h2 class="mb-3">Accede a las principales secciones</h2>
            <div class="d-flex justify-content-center">
                <a href="{{ route('almacenes.index') }}" class="btn btn-primary btn-lg mx-2">Almacenes</a>
                <a href="{{ route('movimientosInventario.index') }}" class="btn btn-success btn-lg mx-2">Movimientos Inventario</a>
                <a href="{{ route('productos.index') }}" class="btn btn-info btn-lg mx-2">Productos</a>
                <a href="{{ route('categorias.index') }}" class="btn btn-warning btn-lg mx-2">Categorías</a>
                <a href="{{ route('proveedores.index') }}" class="btn btn-danger btn-lg mx-2">Proveedores</a>
            </div>
        </div>
    </div>

</div>
@endsection
