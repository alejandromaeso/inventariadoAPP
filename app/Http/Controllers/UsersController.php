<?php

namespace App\Http\Controllers;

use App\Models\Almacenes;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Importa la fachada Hash
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index()
    {
        // Obtenemos todos los usuarios de la base de datos
        // Los ordenamos por nombre
        $users = User::orderBy('name')
                     ->paginate(10);

        return view('usuarios.index', [
            // Devolvemos la colección de usuarios paginados
            'users' => $users,
        ]);
    }

    //Vista para la creación de un usuario
    public function create(): View
    {
        // Obtener todos los almacenes
        $almacenes = Almacenes::orderBy('nombre')->get();

        return view('auth.register', [
            'almacenes' => $almacenes,
        ]);
    }

    /**
     * Maneja la petición para crear un nuevo usuario por parte de un administrador.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            //Validamos si puede ser administrador
            'is_admin' => ['boolean'],
            'almacen_id' => ['nullable', 'exists:almacenes,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Asignamos si es administrador
            'is_admin' => $request->boolean('is_admin', false),
            'almacen_id' => $request->almacen_id,
        ]);

         return redirect()->route('usuarios')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user): View
    {
        // Obtenemos todos los almacenes para el dropdown
        $almacenes = Almacenes::orderBy('nombre')->get();

        // Pasamos el usuario y la lista de almacenes a la vista de edición
        return view('usuarios.edit', [
            'user' => $user,
            'almacenes' => $almacenes,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            // Validar si se cambia de administrador
            'is_admin' => ['boolean'],
            'almacen_id' => ['nullable', 'exists:almacenes,id'],
        ];

        $validatedData = $request->validate($rules);

        // Actualizar los campos name y email
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Si se proporcionó una nueva contraseña, la actualizamos
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Si el campo is_admin existe en la petición, lo actualizamos
        $user->is_admin = $request->has('is_admin');

        $user->almacen_id = $request->almacen_id;

        // Guardar los cambios en la base de datos
        $user->save();

        // Redirigimos a la lista de usuarios con un mensaje de éxito
        return redirect()->route('usuarios')->with('success', 'Usuario actualizado exitosamente.');
    }

    // Función para elimianr un usuario
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('usuarios')->with('success', 'Usuario eliminado exitosamente.');
    }

}
