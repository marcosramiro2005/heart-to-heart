<?php

namespace App\Http\Controllers;

use App\Models\EmotionalRecord;
use App\Models\DiaryEntry;
use App\Models\UserChallenge;
use App\Models\WellnessTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Estadísticas generales
        $totalEmociones  = EmotionalRecord::where('user_id', $userId)->count();
        $totalDiario     = class_exists(DiaryEntry::class)
            ? DiaryEntry::where('user_id', $userId)->count()
            : 0;
        $retosCompletados = UserChallenge::where('user_id', $userId)
            ->where('status', 'completed')->count();
        $retosActivos    = UserChallenge::where('user_id', $userId)
            ->where('status', 'active')->count();

        // Último test
        $ultimoTest = WellnessTest::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();

        // Logros
        $logros = $user->achievements()
            ->orderByDesc('user_achievements.unlocked_at')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'nombre'      => $a->name ?? $a->nombre ?? 'Logro',
                'descripcion' => $a->description ?? $a->descripcion ?? '',
                'emoji'       => $a->emoji ?? '🏆',
                'fecha'       => $a->pivot->unlocked_at
                    ? \Carbon\Carbon::parse($a->pivot->unlocked_at)->format('d/m/Y')
                    : null,
            ]);

        // Nivel
        $nivel     = $user->nivelActual();
        $racha     = $user->rachaActual();
        $numLogros = $logros->count();

        $niveles = [
            'Semilla'              => ['min' => 0,  'max' => 0,  'emoji' => '🌱', 'siguiente' => 'Iniciado',           'necesita' => 1],
            'Iniciado'             => ['min' => 1,  'max' => 2,  'emoji' => '🌿', 'siguiente' => 'Aprendiz',           'necesita' => 3],
            'Aprendiz'             => ['min' => 3,  'max' => 5,  'emoji' => '🌳', 'siguiente' => 'Explorador',         'necesita' => 6],
            'Explorador'           => ['min' => 6,  'max' => 8,  'emoji' => '⭐', 'siguiente' => 'Experto Emocional',  'necesita' => 9],
            'Experto Emocional'    => ['min' => 9,  'max' => 11, 'emoji' => '💫', 'siguiente' => 'Maestro',            'necesita' => 12],
            'Maestro del Bienestar'=> ['min' => 12, 'max' => 99, 'emoji' => '🏆', 'siguiente' => null,                 'necesita' => 0],
        ];

        $infoNivel      = $niveles[$nivel] ?? $niveles['Semilla'];
        $progresNivel   = $infoNivel['necesita'] > 0
            ? min(100, round(($numLogros - $infoNivel['min']) / ($infoNivel['necesita'] - $infoNivel['min']) * 100))
            : 100;

        // Emociones recientes
        $emocionesRecientes = EmotionalRecord::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'emotion'   => $e->emotion,
                'intensity' => $e->intensity,
                'fecha'     => $e->created_at->diffForHumans(),
            ]);

        return Inertia::render('Profile/Show', [
            'usuario'            => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'avatar'    => $user->avatar ?? '👤',
                'bio'       => $user->bio ?? '',
                'location'  => $user->location ?? '',
                'creado'    => $user->created_at->format('d/m/Y'),
                'objetivo'  => $user->objetivo_principal ?? 'general',
            ],
            'stats'              => [
                'emociones_registradas' => $totalEmociones,
                'entradas_diario'       => $totalDiario,
                'retos_completados'     => $retosCompletados,
                'retos_activos'         => $retosActivos,
                'racha_actual'          => $racha,
                'total_logros'          => $numLogros,
            ],
            'nivel'              => $nivel,
            'infoNivel'          => $infoNivel,
            'progresNivel'       => $progresNivel,
            'logros'             => $logros,
            'emocionesRecientes' => $emocionesRecientes,
            'ultimoTest'         => $ultimoTest ? [
                'puntuacion'  => $ultimoTest->puntuacion,
                'nivel'       => $ultimoTest->nivel,
                'fecha'       => $ultimoTest->created_at->diffForHumans(),
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'bio'      => 'nullable|string|max:300',
            'location' => 'nullable|string|max:100',
            'avatar'   => 'nullable|string|max:10',
        ]);

        auth()->user()->update([
            'name'     => $request->name,
            'bio'      => $request->bio,
            'location' => $request->location,
            'avatar'   => $request->avatar ?? auth()->user()->avatar,
        ]);

        return back()->with('success', '✅ Perfil actualizado correctamente');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '🔐 Contraseña actualizada correctamente');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta']);
        }

        auth()->user()->delete();
        auth()->logout();

        return redirect('/')->with('success', 'Cuenta eliminada correctamente');
    }
}