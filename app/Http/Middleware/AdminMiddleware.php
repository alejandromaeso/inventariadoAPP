<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in AND if the logged-in user is an admin
        // Auth::check() checks if a user is logged in
        // Auth::user() gets the logged-in user model instance
        // ->is_admin accesses the column we just added
        if (Auth::check() && Auth::user()->is_admin) {
            // User is logged in and is an admin, proceed with the request
            return $next($request);
        }

        // If the user is not logged in, redirect them to the login page
        if (!Auth::check()) {
            // Optional: Add a message to the session
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        } else {
            // User is logged in but is NOT an admin, redirect them elsewhere (e.g., homepage)
            // Optional: Add a message to the session
            return redirect('/')->with('error', 'No tienes permisos de administrador para acceder a esta página.');
        }

        // Alternative: Abort with a 403 Forbidden response (less user-friendly)
        // abort(403, 'Acceso no autorizado. Solo administradores.');
    }
}
