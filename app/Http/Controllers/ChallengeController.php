<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    public function index()
    {
        $userId  = auth()->id();

        $retosDisponibles = Challenge::all()->map(function ($reto) use ($userId) {
            $userChallenge = UserChallenge::where('user_id', $userId)
                ->where('challenge_id', $reto->id)
                ->first();

            return [
                'id'            => $reto->id,
                'title'         => $reto->title,
                'description'   => $reto->description,
                'type'          => $reto->type,
                'category'      => $reto->category,
                'duration_days' => $reto->duration_days,
                'emoji'         => $reto->emoji,
                'color'         => $reto->color,
                'user_challenge' => $userChallenge ? [
                    'id'             => $userChallenge->id,
                    'status'         => $userChallenge->status,
                    'current_day'    => $userChallenge->current_day,
                    'completed_days' => $userChallenge->completed_days,
                    'started_at'     => $userChallenge->started_at->format('d/m/Y'),
                    'progreso'       => $userChallenge->progresoPorcentaje(),
                    'dias_restantes' => $userChallenge->diasRestantes(),
                ] : null,
            ];
        });

        $misRetos = UserChallenge::where('user_id', $userId)
            ->where('status', 'active')
            ->with('challenge')
            ->get()
            ->map(fn($uc) => [
                'id'             => $uc->id,
                'challenge'      => [
                    'title'         => $uc->challenge->title,
                    'emoji'         => $uc->challenge->emoji,
                    'color'         => $uc->challenge->color,
                    'duration_days' => $uc->challenge->duration_days,
                ],
                'current_day'    => $uc->current_day,
                'completed_days' => $uc->completed_days,
                'started_at'     => $uc->started_at->format('d/m/Y'),
                'progreso'       => $uc->progresoPorcentaje(),
                'dias_restantes' => $uc->diasRestantes(),
                'completado_hoy' => in_array(
                    now()->toDateString(),
                    $uc->completed_days ?? []
                ),
            ]);

        $retosCompletados = UserChallenge::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('challenge')
            ->count();

        return Inertia::render('Challenges/Index', [
            'retosDisponibles' => $retosDisponibles,
            'misRetos'         => $misRetos,
            'retosCompletados' => $retosCompletados,
        ]);
    }

    public function unirse(Challenge $challenge)
    {
        $userId = auth()->id();

        $existe = UserChallenge::where('user_id', $userId)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'active')
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya estás participando en este reto.');
        }

        UserChallenge::updateOrCreate(
            ['user_id' => $userId, 'challenge_id' => $challenge->id],
            [
                'started_at'     => now()->toDateString(),
                'current_day'    => 1,
                'completed_days' => [],
                'status'         => 'active',
            ]
        );

        return back()->with('success', '¡Te has unido al reto! 🎯');
    }

    public function completarDia(UserChallenge $userChallenge)
    {
        if ($userChallenge->user_id !== auth()->id()) {
            abort(403);
        }

        $hoy  = now()->toDateString();
        $dias = $userChallenge->completed_days ?? [];

        if (in_array($hoy, $dias)) {
            return back()->with('info', 'Ya completaste el día de hoy.');
        }

        $dias[] = $hoy;
        $userChallenge->completed_days = $dias;
        $userChallenge->current_day    = count($dias);

        if (count($dias) >= $userChallenge->challenge->duration_days) {
            $userChallenge->status       = 'completed';
            $userChallenge->completed_at = now()->toDateString();
        }

        $userChallenge->save();

        $mensaje = $userChallenge->status === 'completed'
            ? '🏆 ¡Has completado el reto! Increíble.'
            : '✅ ¡Día completado! Sigue así.';

        return back()->with('success', $mensaje);
    }

    public function abandonar(UserChallenge $userChallenge)
    {
        if ($userChallenge->user_id !== auth()->id()) {
            abort(403);
        }

        $userChallenge->update(['status' => 'abandoned']);

        return back()->with('success', 'Reto abandonado. Puedes volver a unirte cuando quieras.');
    }
}