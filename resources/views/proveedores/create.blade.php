@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1><i class="fas fa-plus-circle me-2"></i> Añadir Nuevo Proveedor</h1>
                </div>

                <div class="card-body">
                    {{-- Mostrar errores generales de validación --}}
                     @if ($errors->any() && !$errors->has('nombre') && !$errors->has('direccion') && !$errors->has('telefono') && !$errors->has('email') )
                         <div class="alert alert-danger">
                             <ul>
                                 @foreach ($errors->all() as $error)
                                     <li>{{ $error }}</li>
                                 @endforeach
                             </ul>
                         </div>
                     @endif


                    <form action="{{ route('proveedores.store') }}" method="POST">
                        @csrf

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre') }}"
                                   required
                                   aria-describedby="nombreHelp">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="nombreHelp" class="form-text">Nombre completo o razón social del proveedor.</div>
                        </div>

                        {{-- Dirección --}}
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text"
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   id="direccion"
                                   name="direccion"
                                   value="{{ old('direccion') }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Teléfono (formato español "+34") --}}
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono') }}"
                                   placeholder="+34 6XX XXX XXX / 9XX XXX XXX"
                                   aria-describedby="telefonoHelp">
                             @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="telefonoHelp" class="form-text">Formato español (fijo o móvil), prefijo +34 opcional.</div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="ejemplo@dominio.com">
                             @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                             <a href="{{ route('proveedores.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Guardar Proveedor
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
