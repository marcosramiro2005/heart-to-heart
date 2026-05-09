<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

// Controlador de registro de nuevos usuarios.
// Al registrarse, el usuario es marcado como verificado automáticamente (sin necesitar email de verificación)
// y es redirigido al login para que inicie sesión con sus credenciales recién creadas.
class RegisteredUserController extends Controller
{
    // Muestra el formulario de registro (/register)
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    // Procesa el formulario de registro: valida datos, crea el usuario y lo redirige al login.
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            // lowercase: el email se normaliza a minúsculas; unique: no puede haber dos emails iguales
            'email'    => 'required|string|lowercase|email|max:255|unique:'.User::class,
            // Rules\Password::defaults() aplica las reglas de contraseña configuradas en AppServiceProvider
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            // Hash::make convierte la contraseña plana a un hash bcrypt antes de guardarla
            'password' => Hash::make($request->password),
        ]);

        // Marcar el email como verificado directamente (sin enviar email de confirmación)
        $user->markEmailAsVerified();

        // El evento Registered puede disparar listeners de bienvenida (notificaciones, etc.)
        // Se envuelve en try/catch para que un fallo de email no rompa el registro
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de bienvenida: ' . $e->getMessage());
        }

        // Redirigir al login en vez de auto-autenticar para que el usuario inicie sesión
        return redirect()->route('login');
    }
}
