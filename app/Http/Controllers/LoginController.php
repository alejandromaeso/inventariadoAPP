<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // This request class handles the validation and authentication logic
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // This method simply returns the view file for the login form.
        // The view path 'auth.login' typically corresponds to resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // The LoginRequest class handles the validation of email/password
        // AND attempts to authenticate the user. If validation or authentication fails,
        // it automatically redirects back with errors.
        $request->authenticate();

        // Regenerate the session to prevent session fixation attacks
        $request->session()->regenerate();

        // Redirect the user after successful login.
        // route('dashboard') is the default redirection, you can change this
        // in the config/fortify.php file (if you're using Fortify which Breeze uses)
        // or sometimes configured directly in the LoginRequest or redirection logic.
        // A common practice is to redirect to a 'home' or 'dashboard' route.
        // You can change the default redirect location after login in App\Providers\RouteServiceProvider
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log the user out using the Auth facade
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Redirect the user to the homepage after logout
        return redirect('/');
    }
}
