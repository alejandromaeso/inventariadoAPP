@extends('layouts.app')
@section('title', 'Inicio - Inventariado AMC')

@push('styles')
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
@endpush

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="display-4">Bienvenido a la Aplicación de Inventariado AMC</h1>
            <p class="lead">Gestiona fácilmente tus almacenes, productos, movimientos de inventario, categorías y proveedores.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h2 class="mb-3">Accede a las principales secciones</h2>
            {{--La redirección al *cargar la página* la manejamos en el controlador.--}}
            <div class="d-flex justify-content-center flex-wrap">
                @auth
                     @php $user = Auth::user(); @endphp
                     <a href="{{ ($user->isAdmin() || $user->almacen_id === null) ? route('almacenes.index') : route('productos.indexByAlmacen', $user->almacen_id) }}"
                        class="btn btn-primary btn-lg mx-2 my-1">
                        {{ ($user->isAdmin() || $user->almacen_id === null) ? 'Almacenes' : 'Mi Almacén' }}
                    </a>
                @else
                     {{-- Si no está autenticado, redigiremos al login --}}
                     <a href="{{ route('login') }}" class="btn btn-primary btn-lg mx-2 my-1">Almacenes</a>
                @endauth

                {{-- Enlaces solo para admin --}}
                @auth
                     @php $user = Auth::user(); @endphp
                    @if ($user->isAdmin())
                        <a href="{{ route('proveedores.index') }}" class="btn btn-danger btn-lg mx-2 my-1">Proveedores</a>
                        <a href="{{ route('categorias.index') }}" class="btn btn-warning btn-lg mx-2 my-1">Categorías</a>
                        <a href="{{ route('movimientos.index') }}" class="btn btn-success btn-lg mx-2 my-1">Movimientos Inventario</a>
                        <a href="{{ route('usuarios') }}" class="btn btn-secondary btn-lg mx-2 my-1">Usuarios</a>
                    @endif
                    {{-- Productos lo ve cualquiera autenticado --}}
                     <a href="{{ route('productos.index') }}" class="btn btn-info btn-lg mx-2 my-1">Productos</a>
                @else
                     {{-- Aquí añadiríamos enlaces para invitados, en nuestro caso ninguno --}}
                @endauth


            </div>
        </div>
    </div>

</div>
@endsection
