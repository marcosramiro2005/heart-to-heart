<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

// Controlador estándar de Breeze para la gestión del perfil básico del usuario.
// Para el perfil personalizado con stats, logros y emociones ver UserProfileController.
class ProfileController extends Controller
{
    // Muestra el formulario de edición del perfil (nombre, email).
    // Pasa 'mustVerifyEmail' para que la vista sepa si tiene que mostrar el aviso de verificación.
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    // Actualiza el nombre y email del usuario.
    // Si el email cambia, se resetea email_verified_at para forzar una nueva verificación.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Si el email ha cambiado, invalidar la verificación actual para que vuelva a verificar
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    // Elimina la cuenta del usuario permanentemente.
    // Requiere la contraseña actual como confirmación de seguridad antes de borrar.
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Cerrar sesión antes de eliminar para evitar referencias huérfanas
        Auth::logout();
        $user->delete();

        // Invalidar la sesión y regenerar el token CSRF para limpiar el estado
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
