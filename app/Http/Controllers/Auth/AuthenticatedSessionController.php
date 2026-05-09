<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

// Controlador de inicio y cierre de sesión (login / logout).
// El login usa LoginRequest que maneja la lógica de autenticación con throttling incluido.
class AuthenticatedSessionController extends Controller
{
    // Muestra el formulario de login (/login).
    // Pasa canResetPassword para mostrar el enlace "¿Olvidaste tu contraseña?" solo si la ruta existe.
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'), // mensaje de estado tras un reset de contraseña
        ]);
    }

    // Procesa el login: autentica al usuario y regenera la sesión para prevenir session fixation.
    // LoginRequest::authenticate() lanza ValidationException si las credenciales no son válidas.
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Regenerar el ID de sesión tras autenticarse (medida de seguridad estándar)
        $request->session()->regenerate();

        // redirect()->intended() lleva al usuario a la URL que intentaba visitar antes del login,
        // o al home si no había una URL previa guardada
        return redirect()->intended(route('home', absolute: false));
    }

    // Cierra la sesión del usuario: hace logout, invalida la sesión y regenera el token CSRF.
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Invalidar la sesión elimina todos los datos guardados en ella
        $request->session()->invalidate();
        // Regenerar el token CSRF evita que tokens antiguos puedan usarse tras el logout
        $request->session()->regenerateToken();

        return redirect('/');
    }
}