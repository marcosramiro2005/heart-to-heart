<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HeartyController; 
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EmotionalDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ResourceLibraryController;
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
    Route::get('/perfil', [UserProfileController::class, 'index'])->name('profile.show');
    Route::patch('/perfil', [UserProfileController::class, 'update'])->name('profile.update');
    Route::patch('/perfil/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/perfil', [UserProfileController::class, 'deleteAccount'])->name('profile.delete');
    Route::get('/hearty', [HeartyController::class, 'index'])->name('hearty');
    Route::get('/hearty/inicio', [HeartyController::class, 'inicio']);
    Route::post('/hearty/chat', [HeartyController::class, 'chat']);
    Route::delete('/hearty/limpiar', [HeartyController::class, 'limpiarChat'])->name('hearty.limpiar');
    Route::get('/biblioteca',              [ResourceLibraryController::class, 'index'])->name('library.index');
    Route::get('/biblioteca/guardados',    [ResourceLibraryController::class, 'guardados'])->name('library.saved');
    Route::get('/biblioteca/{resource}',   [ResourceLibraryController::class, 'show'])->name('library.show');
    Route::post('/biblioteca/{resource}/guardar', [ResourceLibraryController::class, 'toggleSave'])->name('library.save');
});

Route::get('/quienes-somos', function () {
    return Inertia::render('About/Index');
})->middleware(['auth'])->name('about');

// Técnicas — todas
Route::middleware(['auth'])->group(function () {
    Route::get('/respiracion',       fn() => Inertia::render('Tecnicas/Respiracion'))->name('respiracion');
    Route::get('/meditacion',        fn() => Inertia::render('Tecnicas/Meditacion'))->name('meditacion');
    Route::get('/sonidos',           fn() => Inertia::render('Tecnicas/Sonidos'))->name('sonidos');
    Route::get('/diario',            fn() => Inertia::render('Tecnicas/Diario'))->name('diario');
    Route::get('/infusiones',        fn() => Inertia::render('Tecnicas/Infusiones'))->name('infusiones');
    Route::get('/ejercicio',         fn() => Inertia::render('Tecnicas/Ejercicio'))->name('ejercicio');
    Route::get('/tapping',           fn() => Inertia::render('Tecnicas/Tapping'))->name('tapping');
    Route::get('/visualizacion',     fn() => Inertia::render('Tecnicas/Visualizacion'))->name('visualizacion');
    Route::get('/yoga',              fn() => Inertia::render('Tecnicas/Yoga'))->name('yoga');
    Route::get('/journaling',        fn() => Inertia::render('Tecnicas/Journaling'))->name('journaling');
    Route::get('/tecnica-5-4-3-2-1', fn() => Inertia::render('Tecnicas/Grounding'))->name('grounding');
    Route::get('/autocompasion',     fn() => Inertia::render('Tecnicas/Autocompasion'))->name('autocompasion');
    Route::get('/relajacion-muscular', fn() => Inertia::render('Tecnicas/RelajacionMuscular'))->name('relajacion_muscular');
    Route::get('/musicoterapia',     fn() => Inertia::render('Tecnicas/Musicoterapia'))->name('musicoterapia');
    Route::get('/gratitud-visual',   fn() => Inertia::render('Tecnicas/GratitudVisual'))->name('gratitud_visual');
});

require __DIR__.'/auth.php';