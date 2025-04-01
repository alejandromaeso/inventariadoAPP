@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Añadir Nuevo Producto</h1>

    {{-- Mostrar errores generales de validación si los hay --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        {{-- Campos estándar: Nombre, Descripción, Precio --}}
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('precio') is-invalid @enderror" id="precio" name="precio" value="{{ old('precio') }}" required min="0" step="0.01">
            @error('precio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo para seleccionar la categoría --}}
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
            <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id" required>
                <option value="">Seleccionar categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
            @error('categoria_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Sección para Almacenes y Cantidades Individuales --}}
        <div class="mb-3">
            <label class="form-label">Asignar Stock Inicial por Almacén <span class="text-danger">*</span></label>
            @error('almacenes') {{-- Error si no se selecciona ningún almacén --}}
                <div class="text-danger d-block mb-2">{{ $message }}</div>
            @enderror

            <div class="border p-3 rounded">
                @forelse($almacenes as $almacen)
                    <div class="form-check mb-2">
                        {{-- Checkbox para seleccionar el almacén --}}
                        <input class="form-check-input @error('almacenes.' . $almacen->id) is-invalid @enderror"
                               type="checkbox"
                               name="almacenes[]" {{-- Envía un array de IDs de almacenes seleccionados --}}
                               value="{{ $almacen->id }}"
                               id="almacen_{{ $almacen->id }}"
                               {{-- Mantiene el check si hubo error de validación --}}
                               {{ (is_array(old('almacenes')) && in_array($almacen->id, old('almacenes'))) ? 'checked' : '' }}>

                        <label class="form-check-label me-2" for="almacen_{{ $almacen->id }}">
                            {{ $almacen->nombre }}
                        </label>

                        {{-- Input para la cantidad de este almacén específico --}}
                        <input type="number"
                               name="cantidades[{{ $almacen->id }}]"
                               value="{{ old('cantidades.' . $almacen->id, 0) }}"
                               min="0"
                               class="form-control form-control-sm d-inline-block @error('cantidades.' . $almacen->id) is-invalid @enderror"
                               style="width: 100px;"
                               aria-label="Cantidad para {{ $almacen->nombre }}">
                        @error('cantidades.' . $almacen->id)
                           <div class="invalid-feedback d-inline-block">{{ $message }}</div>
                        @enderror
                    </div>
                @empty
                    <p class="text-muted">No hay almacenes disponibles para asignar.</p>
                @endforelse
            </div>
            <small class="form-text text-muted">Selecciona al menos un almacén e indica la cantidad inicial.</small>
        </div>


        <button type="submit" class="btn btn-success mt-3">Guardar Producto</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
