<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

/**
 * ChallengeController
 *
 * Gestiona el sistema de retos de bienestar:
 * - Lista todos los retos disponibles indicando si el usuario participa en cada uno
 * - Permite unirse a un reto (crea un UserChallenge en estado 'active')
 * - Permite marcar el día actual como completado dentro de un reto
 * - Permite abandonar un reto activo (estado 'abandoned')
 */
class ChallengeController extends Controller
{
    /**
     * Muestra la página de retos con tres secciones:
     * - Todos los retos disponibles (con el progreso del usuario si participa)
     * - Los retos activos del usuario
     * - El número de retos completados históricamente
     */
    public function index()
    {
        $userId = auth()->id();

        // Para cada reto del sistema, busca si el usuario tiene un UserChallenge asociado
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
                // Si el usuario participa, incluye su progreso; si no, null
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

        // Retos en los que el usuario está participando actualmente (status = 'active')
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
                // Indica si el usuario ya completó el día de hoy en este reto
                'completado_hoy' => in_array(
                    now()->toDateString(),
                    $uc->completed_days ?? []
                ),
            ]);

        // Contador de retos finalizados satisfactoriamente
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

    /**
     * Apunta al usuario a un reto.
     * Si ya está participando activamente en ese reto, devuelve un error.
     * Usa updateOrCreate para evitar duplicados si el usuario se unió anteriormente.
     */
    public function unirse(Challenge $challenge)
    {
        $userId = auth()->id();

        // Comprobar si ya tiene un reto activo con este challenge
        $existe = UserChallenge::where('user_id', $userId)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'active')
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya estás participando en este reto.');
        }

        // Crear o reactivar el UserChallenge desde el día 1
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

    /**
     * Marca el día actual como completado dentro de un reto activo.
     * Si se alcanzan todos los días requeridos, el reto pasa a estado 'completed'.
     * Solo el propietario del UserChallenge puede completar su propio día.
     */
    public function completarDia(UserChallenge $userChallenge)
    {
        // Verificar que el reto pertenece al usuario autenticado
        if ($userChallenge->user_id !== auth()->id()) {
            abort(403);
        }

        $hoy  = now()->toDateString();
        $dias = $userChallenge->completed_days ?? [];

        // Evitar marcar el mismo día dos veces
        if (in_array($hoy, $dias)) {
            return back()->with('info', 'Ya completaste el día de hoy.');
        }

        // Añadir el día de hoy a la lista de días completados
        $dias[] = $hoy;
        $userChallenge->completed_days = $dias;
        $userChallenge->current_day    = count($dias);

        // Si ya se han completado todos los días del reto, marcarlo como terminado
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

    /**
     * Abandona un reto activo cambiando su estado a 'abandoned'.
     * El usuario puede volver a unirse al mismo reto en el futuro.
     * Solo el propietario puede abandonar su propio reto.
     */
    public function abandonar(UserChallenge $userChallenge)
    {
        if ($userChallenge->user_id !== auth()->id()) {
            abort(403);
        }

        $userChallenge->update(['status' => 'abandoned']);

        return back()->with('success', 'Reto abandonado. Puedes volver a unirte cuando quieras.');
    }
}