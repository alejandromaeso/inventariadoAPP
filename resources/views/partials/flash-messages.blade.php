{{-- Mensaje de Éxito --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensaje de Error --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensaje de Advertencia --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>¡Atención!</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensaje de Información --}}
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Información:</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensaje genérico si hay errores de validación del formulario --}}
{{-- Esto es útil si no se muestran todos los errores de validación junto a cada campo --}}
@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error de Validación!</strong> Por favor, corrige los errores indicados en el formulario.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
