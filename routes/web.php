<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HeartyController; 
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EmotionalDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ResourceLibraryController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\WellnessTestController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\WellnessPlanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/mis-estadisticas', function () {
    return Inertia::render('Stats/Index');
})->middleware('auth')->name('stats');

// ── Ruta raíz — Landing si no está logueado, home si sí ──
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return Inertia::render('Landing');
})->name('landing');

// ── Dashboard interno de Breeze ──
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Página principal tras login ──
Route::get('/home', function () {
    return Inertia::render('Home/Index');
})->middleware(['auth'])->name('home');

// ── Perfil de Breeze ──
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Rutas protegidas ──
Route::middleware(['auth'])->group(function () {

    // Hearty
    Route::get('/hearty',              [HeartyController::class, 'index'])->name('hearty');
    Route::get('/hearty/inicio',       [HeartyController::class, 'inicio']);
    Route::post('/hearty/chat',        [HeartyController::class, 'chat']);
    Route::delete('/hearty/limpiar',   [HeartyController::class, 'limpiarChat'])->name('hearty.limpiar');

    // Foro
    Route::get('/comunidad',                        [ForumController::class, 'index'])->name('forum.index');
    Route::get('/comunidad/{forumPost}',             [ForumController::class, 'show'])->name('forum.show');
    Route::post('/comunidad',                        [ForumController::class, 'store'])->name('forum.store');
    Route::post('/comunidad/{forumPost}/comentar',   [ForumController::class, 'comment'])->name('forum.comment');
    Route::post('/comunidad/{forumPost}/like',       [ForumController::class, 'like'])->name('forum.like');
    Route::delete('/comunidad/{forumPost}',          [ForumController::class, 'destroy'])->name('forum.destroy');

    // Noticias y recursos
    Route::get('/recursos',           [NewsController::class, 'index'])->name('news.index');
    Route::get('/recursos/guardados', [NewsController::class, 'guardadas'])->name('news.guardadas');
    Route::post('/recursos/guardar',  [NewsController::class, 'toggleGuardar'])->name('news.guardar');

    // Emociones
    Route::get('/mis-emociones',             [EmotionalDashboardController::class, 'index'])->name('emotional.dashboard');
    Route::post('/mis-emociones/registrar',  [EmotionalDashboardController::class, 'registrar'])->name('emotional.registrar');

    // Logros
    Route::get('/logros',              [AchievementController::class, 'index'])->name('achievements.index');
    Route::post('/logros/verificar',   [AchievementController::class, 'verificar'])->name('achievements.verificar');

    // Perfil de usuario
    Route::get('/perfil',              [UserProfileController::class, 'index'])->name('profile.show');
    Route::patch('/perfil',            [UserProfileController::class, 'update'])->name('profile.update_custom');
    Route::patch('/perfil/password',   [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/perfil',           [UserProfileController::class, 'deleteAccount'])->name('profile.delete');

    // Biblioteca
    Route::get('/biblioteca',                      [ResourceLibraryController::class, 'index'])->name('library.index');
    Route::get('/biblioteca/guardados',            [ResourceLibraryController::class, 'guardados'])->name('library.saved');
    Route::get('/biblioteca/{resource}',           [ResourceLibraryController::class, 'show'])->name('library.show');
    Route::post('/biblioteca/{resource}/guardar',  [ResourceLibraryController::class, 'toggleSave'])->name('library.save');

    // Quiénes somos
    Route::get('/quienes-somos', fn() => Inertia::render('About/Index'))->name('about');

    // Técnicas
    Route::get('/respiracion',         fn() => Inertia::render('Tecnicas/Respiracion'))->name('respiracion');
    Route::get('/meditacion',          fn() => Inertia::render('Tecnicas/Meditacion'))->name('meditacion');
    Route::get('/sonidos',             fn() => Inertia::render('Tecnicas/Sonidos'))->name('sonidos');
    Route::get('/infusiones',          fn() => Inertia::render('Tecnicas/Infusiones'))->name('infusiones');
    Route::get('/ejercicio',           fn() => Inertia::render('Tecnicas/Ejercicio'))->name('ejercicio');
    Route::get('/tapping',             fn() => Inertia::render('Tecnicas/Tapping'))->name('tapping');
    Route::get('/visualizacion',       fn() => Inertia::render('Tecnicas/Visualizacion'))->name('visualizacion');
    Route::get('/yoga',                fn() => Inertia::render('Tecnicas/Yoga'))->name('yoga');
    Route::get('/journaling',          fn() => Inertia::render('Tecnicas/Journaling'))->name('journaling');
    Route::get('/tecnica-5-4-3-2-1',   fn() => Inertia::render('Tecnicas/Grounding'))->name('grounding');
    Route::get('/autocompasion',       fn() => Inertia::render('Tecnicas/Autocompasion'))->name('autocompasion');
    Route::get('/relajacion-muscular', fn() => Inertia::render('Tecnicas/RelajacionMuscular'))->name('relajacion_muscular');
    Route::get('/musicoterapia',       fn() => Inertia::render('Tecnicas/Musicoterapia'))->name('musicoterapia');
    Route::get('/gratitud-visual',     fn() => Inertia::render('Tecnicas/GratitudVisual'))->name('gratitud_visual');
    Route::get('/retos',                                   [ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/retos/{challenge}/unirse',               [ChallengeController::class, 'unirse'])->name('challenges.join');
    Route::post('/retos/{userChallenge}/completar-dia',    [ChallengeController::class, 'completarDia'])->name('challenges.complete');
    Route::post('/retos/{userChallenge}/abandonar',        [ChallengeController::class, 'abandonar'])->name('challenges.abandon');
    Route::get('/test-bienestar',         [WellnessTestController::class, 'index'])->middleware('auth')->name('wellness.index');
    Route::post('/test-bienestar/guardar',[WellnessTestController::class, 'guardar'])->middleware('auth')->name('wellness.guardar');
    Route::get('/onboarding',  [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'completar'])->name('onboarding.completar');
    Route::get('/diario',              [DiaryController::class, 'index'])->name('diary.index');
    Route::post('/diario',             [DiaryController::class, 'guardar'])->name('diary.guardar');
    Route::delete('/diario/{diaryEntry}', [DiaryController::class, 'eliminar'])->name('diary.eliminar');
    Route::get('/focus', fn() => Inertia::render('Focus/Index'))->middleware('auth')->name('focus');
    Route::get('/mi-plan',                          [WellnessPlanController::class, 'index'])->name('plan.index');
    Route::post('/mi-plan/generar',                 [WellnessPlanController::class, 'generar'])->name('plan.generar');
    Route::post('/mi-plan/{wellnessPlan}/completar',[WellnessPlanController::class, 'completarDia'])->name('plan.completar');
    Route::get('/sos', fn() => Inertia::render('SOS/Index'))->name('sos');
});

require __DIR__.'/auth.php';