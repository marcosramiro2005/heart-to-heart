<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HeartyController; 
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EmotionalDashboardController;
use App\Http\Controllers\AchievementController;
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

Route::middleware(['auth'])->group(function () {

    // Foro y noticias
    Route::get('/comunidad', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/comunidad/{forumPost}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/comunidad', [ForumController::class, 'store'])->name('forum.store');
    Route::post('/comunidad/{forumPost}/comentar', [ForumController::class, 'comment'])->name('forum.comment');
    Route::post('/comunidad/{forumPost}/like', [ForumController::class, 'like'])->name('forum.like');
    Route::delete('/comunidad/{forumPost}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::get('/recursos', [NewsController::class, 'index'])->name('news.index');
    Route::get('/recursos/guardados', [NewsController::class, 'guardadas'])->name('news.guardadas');
    Route::post('/recursos/guardar', [NewsController::class, 'toggleGuardar'])->name('news.guardar');
    Route::get('/mis-emociones', [EmotionalDashboardController::class, 'index'])->name('emotional.dashboard');
    Route::post('/mis-emociones/registrar', [EmotionalDashboardController::class, 'registrar'])->name('emotional.registrar');
    Route::get('/logros', [AchievementController::class, 'index'])->name('achievements.index');
    Route::post('/logros/verificar', [AchievementController::class, 'verificar'])->name('achievements.verificar');
});

require __DIR__.'/auth.php';