<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedoresController extends Controller
{
    public function index()
    {
        $proveedores = Proveedores::all();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => [ // Reglas específicas para el teléfono
                'nullable',
                'string',
                'max:20',
                // Expresión regular para validar formatos españoles:
                // - Opcional prefijo +34 o 0034
                // - Seguido opcionalmente por espacios/guiones/puntos
                // - Debe empezar por 6, 7 o 9
                // - Seguido por 8 dígitos más
                // - Permite espacios/guiones/puntos opcionales entre los dígitos
                'regex:/^((?:\+|00)34)?[\s.-]*[679]([\s.-]?\d){8}$/'
            ],
            'email' => 'nullable|email|max:255|unique:proveedores,email',
        ], [
            // Mensaje de errores
            'telefono.regex' => 'El formato del teléfono no es válido. Use un formato español.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.unique' => 'Este correo electrónico ya está en uso.'
        ]);

        // Antes de guardar, normalizamos el número a un formato estándar,
        // como el +34XXXXXXXXX
        if (!empty($validatedData['telefono'])) {
            $telefono = $validatedData['telefono'];
            // Eliminamos espacios, guiones y puntos
            $telefonoNormalizado = preg_replace('/[\s.-]+/', '', $telefono);

            // Reemplazamos 0034 inicial por +34
            if (strpos($telefonoNormalizado, '0034') === 0) {
                $telefonoNormalizado = '+34' . substr($telefonoNormalizado, 4);
            }

            // Añadimos +34 si son 9 dígitos y empiezan por 6, 7 o 9
            if (preg_match('/^[679]\d{8}$/', $telefonoNormalizado)) {
                $telefonoNormalizado = '+34' . $telefonoNormalizado;
            }

            // Asignamos el valor normalizado solo si cumple el formato español
            if (preg_match('/^\+34[679]\d{8}$/', $telefonoNormalizado)) {
                 $validatedData['telefono'] = $telefonoNormalizado;
            } else {
                 // Si después de normalizar no coincide (ej. teléfono internacional),
                 // guardamos la versión que pasa el regex inicial.
                 // Guardamos solo dígitos y el + si existe
                  $validatedData['telefono'] = preg_replace('/[^\d+]/', '', $telefonoNormalizado);
            }
        }
        // --------------------------------------------------

        // Usamos $validatedData que puede tener el teléfono normalizado
        Proveedores::create($validatedData);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor creado correctamente.');
    }

    public function show(Proveedores $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedores $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedores $proveedor)
    {
        // Validación
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => [ // Reglas específicas para el teléfono (igual que en store)
                'nullable',
                'string',
                'max:20',
                // Regex para validar formatos españoles
                'regex:/^((?:\+|00)34)?[\s.-]*[679]([\s.-]?\d){8}$/'
            ],
            // Validación de email: debe ser único EXCEPTO para este proveedor actual
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                 Rule::unique('proveedores', 'email')->ignore($proveedor->id),
            ],
        ], [
            // Mensajes de error
            'telefono.regex' => 'El formato del teléfono no es válido. Use un formato español (9 dígitos empezando por 6, 7 o 9, opcionalmente con +34 y separadores).',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.unique' => 'Este correo electrónico ya está siendo utilizado por otro proveedor.'
        ]);

        // Validación del Teléfono (Igual que en store)
        if (!empty($validatedData['telefono'])) {
            $telefono = $validatedData['telefono'];
            $telefonoNormalizado = preg_replace('/[\s.-]+/', '', $telefono);

            if (strpos($telefonoNormalizado, '0034') === 0) {
                $telefonoNormalizado = '+34' . substr($telefonoNormalizado, 4);
            }

            if (preg_match('/^[679]\d{8}$/', $telefonoNormalizado)) {
                $telefonoNormalizado = '+34' . $telefonoNormalizado;
            }

            if (preg_match('/^\+34[679]\d{8}$/', $telefonoNormalizado)) {
                 $validatedData['telefono'] = $telefonoNormalizado;
            } else {
                  $validatedData['telefono'] = preg_replace('/[^\d+]/', '', $telefonoNormalizado);
            }
        } elseif (array_key_exists('telefono', $validatedData)) {
             $validatedData['telefono'] = $validatedData['telefono'] === '' ? null : $validatedData['telefono'];
        }

        // Usamos $validatedData que contiene los datos validados y normalizados
        $proveedor->update($validatedData);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor actualizado correctamente.');
    }

    // Eliminar proveedores
    public function destroy(Proveedores $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
