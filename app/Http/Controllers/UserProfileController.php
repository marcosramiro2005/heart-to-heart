<?php

namespace App\Http\Controllers;

use App\Models\EmotionalRecord;
use App\Models\BreathingSession;
use App\Models\ForumPost;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // ── Estadísticas de actividad ──
        $stats = [
            'total_registros'  => EmotionalRecord::where('user_id', $userId)->count(),
            'sesiones_respira' => BreathingSession::where('user_id', $userId)->count(),
            'posts_foro'       => ForumPost::where('user_id', $userId)->count(),
            'mensajes_hearty'  => ChatMessage::where('user_id', $userId)
                                    ->where('sender', 'user')->count(),
            'logros'           => $user->achievements()->count(),
            'puntos'           => $user->totalPoints(),
            'miembro_desde'    => Carbon::parse($user->created_at)->format('d/m/Y'),
            'dias_activo'      => EmotionalRecord::where('user_id', $userId)
                                    ->distinct('recorded_at')->count(),
        ];

        // ── Historial emocional reciente ──
        $historialEmocional = EmotionalRecord::where('user_id', $userId)
            ->orderByDesc('recorded_at')
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'emotion'     => $r->emotion,
                'intensity'   => $r->intensity,
                'color'       => $r->color ?? '#4ECDC4',
                'recorded_at' => Carbon::parse($r->recorded_at)->format('d/m/Y'),
            ]);

        // ── Últimos posts del foro ──
        $ultimosPosts = ForumPost::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'title'      => $p->title,
                'category'   => $p->category,
                'likes'      => $p->likes_count,
                'created_at' => $p->created_at->diffForHumans(),
            ]);

        // ── Logros desbloqueados ──
        $logros = $user->achievements()
            ->orderByPivot('unlocked_at', 'desc')
            ->take(6)
            ->get()
            ->map(fn($l) => [
                'name'  => $l->name,
                'emoji' => $l->emoji,
                'color' => $l->color,
            ]);

        return Inertia::render('Profile/Index', [
            'usuario'           => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'avatar'         => $user->avatar,
                'bio'            => $user->bio,
                'location'       => $user->location,
                'birth_date'     => $user->birth_date,
                'profile_public' => $user->profile_public,
                'show_in_forum'  => $user->show_in_forum,
                'theme_color'    => $user->theme_color,
                'miembro_desde'  => Carbon::parse($user->created_at)->format('d/m/Y'),
            ],
            'stats'             => $stats,
            'historialEmocional'=> $historialEmocional,
            'ultimosPosts'      => $ultimosPosts,
            'logros'            => $logros,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'bio'            => 'nullable|string|max:300',
            'location'       => 'nullable|string|max:100',
            'birth_date'     => 'nullable|date|before:today',
            'avatar'         => 'nullable|string|max:10',
            'profile_public' => 'boolean',
            'show_in_forum'  => 'boolean',
            'theme_color'    => 'nullable|string|max:7',
        ]);

        auth()->user()->update([
            'name'           => $request->name,
            'bio'            => $request->bio,
            'location'       => $request->location,
            'birth_date'     => $request->birth_date,
            'avatar'         => $request->avatar,
            'profile_public' => $request->profile_public ?? true,
            'show_in_forum'  => $request->show_in_forum ?? true,
            'theme_color'    => $request->theme_color ?? '#4ECDC4',
        ]);

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '¡Contraseña actualizada correctamente!');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'La contraseña no es correcta.']);
        }

        $user = auth()->user();
        auth()->logout();
        $user->delete();

        return redirect('/')->with('success', 'Cuenta eliminada correctamente.');
    }
}