<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Aplicación')</title>
    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Fonts (Nunito) --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">InventariadoAPP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- Enlaces al menú principal --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('almacenes.index') }}">Almacenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('proveedores.index') }}">Proveedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categorias.index') }}">Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('productos.index') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('movimientos.index') }}">Movimientos Inventario</a>
                    </li>

                    {{-- ========================================= --}}
                    {{-- INTEGRACIÓN BREEZE: Enlaces de Autenticación --}}
                    {{-- ========================================= --}}
                    @guest {{-- Si el usuario NO está autenticado --}}
                        {{-- Mostrar Solo el enlace de Login para invitados --}}
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endif
                    @endguest

                    {{-- Mostrar el enlace de Register SOLO si el usuario autenticado es un administrador --}}
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Añadir Usuario</a>
                        </li>
                    @endif


                    @auth {{-- Si el usuario SÍ está autenticado --}}
                        {{-- Dropdown del usuario autenticado --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }} {{-- Muestra el nombre del usuario --}}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                {{-- Enlace al dashboard o perfil --}}
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                {{-- Logout --}}
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>


                    @endauth

                </ul>
            </div>
        </div>
    </nav>

    {{-- Contenedor principal del contenido de la página --}}
    <div class="container mt-4">
        {{-- Yield para mensajes de sesión generales --}}
        {{-- Este bloque muestra mensajes como 'success', 'error', 'status' que pueden venir de redirects --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            {{-- Div para mostrar mensajes de errores generales --}}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Aquí se insertará el contenido específico de cada vista (ej: productos.index, productos.show) --}}
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
