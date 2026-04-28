<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\EmotionalRecord;
use App\Models\BreathingSession;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\SavedNews;
use App\Models\UserAchievement;
use Carbon\Carbon;

/**
 * AchievementService
 *
 * Servicio centralizado para la gestión de logros del usuario.
 * Se encarga de dos operaciones principales:
 *
 * 1. unlock(): Desbloquear un logro concreto si el usuario aún no lo tiene.
 * 2. verificarLogros(): Comprobar todos los logros disponibles y desbloquear
 *    los que el usuario ya merece según su actividad actual.
 *
 * Se llama desde distintos controladores (emociones, respiración, foro, noticias)
 * después de que el usuario realice una acción relevante.
 */
class AchievementService
{
    /**
     * Intenta desbloquear un logro específico para un usuario.
     * Si el logro ya estaba desbloqueado, no hace nada y devuelve null.
     * Si el logro no existe en la BD (código incorrecto), también devuelve null.
     *
     * @param  int     $userId  ID del usuario
     * @param  string  $code    Código único del logro (ej: 'first_day', 'streak_7')
     * @return Achievement|null  El logro desbloqueado, o null si no procede
     */
    public function unlock(int $userId, string $code): ?Achievement
    {
        // Buscar el logro por su código único
        $achievement = Achievement::where('code', $code)->first();
        if (!$achievement) return null;

        // Comprobar si el usuario ya lo tiene desbloqueado para evitar duplicados
        $yaDesbloqueado = UserAchievement::where('user_id', $userId)
            ->where('achievement_id', $achievement->id)
            ->exists();

        if ($yaDesbloqueado) return null;

        // Crear el registro de desbloqueo en la tabla pivote user_achievements
        UserAchievement::create([
            'user_id'        => $userId,
            'achievement_id' => $achievement->id,
            'unlocked_at'    => now(),
        ]);

        return $achievement;
    }

    /**
     * Comprueba todos los logros posibles del usuario y desbloquea los que corresponden.
     * Se evalúan logros de: registros emocionales, rachas, respiración, foro y noticias.
     *
     * @param  int  $userId  ID del usuario a verificar
     * @return array  Lista de logros Achievement recién desbloqueados en esta llamada
     */
    public function verificarLogros(int $userId): array
    {
        $nuevos = [];

        // Precarga todos los logros y los ya desbloqueados en 2 queries (en vez de ~16)
        $todosLosLogros  = Achievement::all()->keyBy('code');
        $desbloqueadosIds = UserAchievement::where('user_id', $userId)
            ->pluck('achievement_id')
            ->flip(); // convierte a colección indexada por ID para búsquedas O(1)

        $intentar = function (string $code) use ($userId, $todosLosLogros, &$desbloqueadosIds, &$nuevos) {
            $achievement = $todosLosLogros->get($code);
            if (!$achievement || $desbloqueadosIds->has($achievement->id)) return;

            UserAchievement::create([
                'user_id'        => $userId,
                'achievement_id' => $achievement->id,
                'unlocked_at'    => now(),
            ]);

            $desbloqueadosIds->put($achievement->id, 0); // evita doble desbloqueo en la misma llamada
            $nuevos[] = $achievement;
        };

        // ── Logros por registros emocionales ──
        $totalRegistros = EmotionalRecord::where('user_id', $userId)->count();
        if ($totalRegistros >= 1)  $intentar('first_day');
        if ($totalRegistros >= 10) $intentar('emotions_10');

        // Logros por racha de días consecutivos con registro emocional
        $racha = $this->calcularRacha($userId);
        if ($racha >= 3)  $intentar('streak_3');
        if ($racha >= 7)  $intentar('streak_7');
        if ($racha >= 30) $intentar('streak_30');

        // ── Logros por sesiones de respiración ──
        $sesiones = BreathingSession::where('user_id', $userId)->count();
        if ($sesiones >= 1) $intentar('first_breath');
        if ($sesiones >= 5) $intentar('breath_5');

        // ── Logros por participación en el foro ──
        $posts = ForumPost::where('user_id', $userId)->count();
        if ($posts >= 1) $intentar('first_post');
        if ($posts >= 5) $intentar('posts_5');

        $comentarios = ForumComment::where('user_id', $userId)->count();
        if ($comentarios >= 1) $intentar('first_comment');

        // ── Logros por noticias guardadas ──
        $guardadas = SavedNews::where('user_id', $userId)->count();
        if ($guardadas >= 1) $intentar('first_news');

        return $nuevos;
    }

    /**
     * Calcula los días consecutivos de racha en los registros emocionales.
     * Compara cada fecha de registro con el día esperado según su posición en la secuencia.
     * Si algún día falta, la racha se rompe y el bucle termina.
     *
     * @param  int  $userId
     * @return int  Número de días consecutivos con registro emocional
     */
    private function calcularRacha(int $userId): int
    {
        // Obtener fechas únicas de registros, de más reciente a más antigua, normalizadas a inicio de día
        $registros = EmotionalRecord::where('user_id', $userId)
            ->select('recorded_at')
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn($r) => Carbon::parse($r->recorded_at)->startOfDay());

        $racha = 0;
        $hoy   = Carbon::today();

        foreach ($registros as $i => $fecha) {
            // Si la fecha del registro i-ésimo coincide con hace exactamente i días, la racha sigue
            if ($fecha->eq($hoy->copy()->subDays($i))) {
                $racha++;
            } else {
                break; // Primer salto encontrado: se rompe la racha
            }
        }

        return $racha;
    }
}