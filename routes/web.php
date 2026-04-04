<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HeartyController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Ruta raíz — redirige según si está autenticado o no
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('hearty');
    }
    return redirect()->route('login');
});

// Dashboard (lo mantenemos por si Breeze lo necesita internamente)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- NUEVAS RUTAS DE HEARTY (Reemplazan a la anterior) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/hearty', [HeartyController::class, 'index'])->name('hearty');
    Route::get('/hearty/inicio', [HeartyController::class, 'inicio']);
    Route::post('/hearty/chat', [HeartyController::class, 'chat']);
});
// --------------------------------------------------------

// Página principal tras login
Route::get('/home', function () {
    return Inertia::render('Home/Index');
})->middleware(['auth'])->name('home');

// Técnicas
Route::get('/respiracion', function () {
    return Inertia::render('Tecnicas/Respiracion');
})->middleware(['auth'])->name('respiracion');

Route::get('/sonidos', function () {
    return Inertia::render('Tecnicas/Sonidos');
})->middleware(['auth'])->name('sonidos');

Route::get('/diario', function () {
    return Inertia::render('Tecnicas/Diario');
})->middleware(['auth'])->name('diario');

Route::get('/infusiones', function () {
    return Inertia::render('Tecnicas/Infusiones');
})->middleware(['auth'])->name('infusiones');

Route::get('/ejercicio', function () {
    return Inertia::render('Tecnicas/Ejercicio');
})->middleware(['auth'])->name('ejercicio');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';