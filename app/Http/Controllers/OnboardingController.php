<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * OnboardingController
 *
 * Gestiona el proceso de bienvenida que se muestra una sola vez al registrarse.
 * El usuario elige su avatar y objetivo principal antes de acceder al home.
 * Una vez completado, el campo 'onboarding_completado' queda en true
 * y el usuario no vuelve a ver esta pantalla.
 */
class OnboardingController extends Controller
{
    /**
     * Muestra la pantalla de onboarding.
     * Si el usuario ya completó el proceso, lo redirige directamente al home.
     */
    public function index()
    {
        // Si ya completó el onboarding, no tiene sentido volver a mostrarlo
        if (auth()->user()->onboarding_completado) {
            return redirect()->route('home');
        }

        return Inertia::render('Onboarding/Index', [
            'userName' => auth()->user()->name,
        ]);
    }

    /**
     * Guarda las preferencias del onboarding y marca el proceso como completado.
     * Avatar y objetivo principal son opcionales; si no se eligen se usan valores por defecto.
     */
    public function completar(Request $request)
    {
        $request->validate([
            'avatar'             => 'nullable|string|max:10',
            'objetivo_principal' => 'nullable|string|max:100',
        ]);

        auth()->user()->update([
            'onboarding_completado' => true,
            // Si el usuario no elige avatar, se usa el emoji genérico de persona
            'avatar'                => $request->avatar ?? '👤',
            'objetivo_principal'    => $request->objetivo_principal,
        ]);

        return redirect()->route('home')->with('success', '¡Bienvenido/a a Heart to Heart! 💚');
    }
}