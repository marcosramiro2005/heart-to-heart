<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    public function index()
    {
        if (auth()->user()->onboarding_completado) {
            return redirect()->route('home');
        }

        return Inertia::render('Onboarding/Index', [
            'userName' => auth()->user()->name,
        ]);
    }

    public function completar(Request $request)
    {
        $request->validate([
            'avatar'            => 'nullable|string|max:10',
            'objetivo_principal'=> 'nullable|string|max:100',
        ]);

        auth()->user()->update([
            'onboarding_completado' => true,
            'avatar'                => $request->avatar ?? '👤',
            'objetivo_principal'    => $request->objetivo_principal,
        ]);

        return redirect()->route('home')->with('success', '¡Bienvenido/a a Heart to Heart! 💚');
    }
}