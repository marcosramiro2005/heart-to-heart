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

        // ── Logros por registros emocionales ──
        $totalRegistros = EmotionalRecord::where('user_id', $userId)->count();

        if ($totalRegistros >= 1)  $this->intentarDesbloquear($userId, 'first_day', $nuevos);
        if ($totalRegistros >= 10) $this->intentarDesbloquear($userId, 'emotions_10', $nuevos);

        // Logros por racha de días consecutivos con registro emocional
        $racha = $this->calcularRacha($userId);
        if ($racha >= 3)  $this->intentarDesbloquear($userId, 'streak_3', $nuevos);
        if ($racha >= 7)  $this->intentarDesbloquear($userId, 'streak_7', $nuevos);
        if ($racha >= 30) $this->intentarDesbloquear($userId, 'streak_30', $nuevos);

        // ── Logros por sesiones de respiración ──
        $sesiones = BreathingSession::where('user_id', $userId)->count();
        if ($sesiones >= 1) $this->intentarDesbloquear($userId, 'first_breath', $nuevos);
        if ($sesiones >= 5) $this->intentarDesbloquear($userId, 'breath_5', $nuevos);

        // ── Logros por participación en el foro ──
        $posts = ForumPost::where('user_id', $userId)->count();
        if ($posts >= 1) $this->intentarDesbloquear($userId, 'first_post', $nuevos);
        if ($posts >= 5) $this->intentarDesbloquear($userId, 'posts_5', $nuevos);

        $comentarios = ForumComment::where('user_id', $userId)->count();
        if ($comentarios >= 1) $this->intentarDesbloquear($userId, 'first_comment', $nuevos);

        // ── Logros por noticias guardadas ──
        $guardadas = SavedNews::where('user_id', $userId)->count();
        if ($guardadas >= 1) $this->intentarDesbloquear($userId, 'first_news', $nuevos);

        return $nuevos;
    }

    /**
     * Llama a unlock() y si el logro se desbloqueó lo añade al array de nuevos logros.
     * Usa el array por referencia (&$nuevos) para acumular los resultados.
     */
    private function intentarDesbloquear(int $userId, string $code, array &$nuevos): void
    {
        $logro = $this->unlock($userId, $code);
        if ($logro) $nuevos[] = $logro;
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