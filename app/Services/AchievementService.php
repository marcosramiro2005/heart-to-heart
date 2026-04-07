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

class AchievementService
{
    // Desbloquear un logro si no lo tiene ya
    public function unlock(int $userId, string $code): ?Achievement
    {
        $achievement = Achievement::where('code', $code)->first();
        if (!$achievement) return null;

        $yaDesbloqueado = UserAchievement::where('user_id', $userId)
            ->where('achievement_id', $achievement->id)
            ->exists();

        if ($yaDesbloqueado) return null;

        UserAchievement::create([
            'user_id'        => $userId,
            'achievement_id' => $achievement->id,
            'unlocked_at'    => now(),
        ]);

        return $achievement;
    }

    // Verificar todos los logros posibles del usuario
    public function verificarLogros(int $userId): array
    {
        $nuevos = [];

        // ── Racha de registros emocionales ──
        $totalRegistros = EmotionalRecord::where('user_id', $userId)->count();

        if ($totalRegistros >= 1)  $this->intentarDesbloquear($userId, 'first_day', $nuevos);
        if ($totalRegistros >= 10) $this->intentarDesbloquear($userId, 'emotions_10', $nuevos);

        $racha = $this->calcularRacha($userId);
        if ($racha >= 3)  $this->intentarDesbloquear($userId, 'streak_3', $nuevos);
        if ($racha >= 7)  $this->intentarDesbloquear($userId, 'streak_7', $nuevos);
        if ($racha >= 30) $this->intentarDesbloquear($userId, 'streak_30', $nuevos);

        // ── Sesiones de respiración ──
        $sesiones = BreathingSession::where('user_id', $userId)->count();
        if ($sesiones >= 1) $this->intentarDesbloquear($userId, 'first_breath', $nuevos);
        if ($sesiones >= 5) $this->intentarDesbloquear($userId, 'breath_5', $nuevos);

        // ── Foro ──
        $posts = ForumPost::where('user_id', $userId)->count();
        if ($posts >= 1) $this->intentarDesbloquear($userId, 'first_post', $nuevos);
        if ($posts >= 5) $this->intentarDesbloquear($userId, 'posts_5', $nuevos);

        $comentarios = ForumComment::where('user_id', $userId)->count();
        if ($comentarios >= 1) $this->intentarDesbloquear($userId, 'first_comment', $nuevos);

        // ── Noticias guardadas ──
        $guardadas = SavedNews::where('user_id', $userId)->count();
        if ($guardadas >= 1) $this->intentarDesbloquear($userId, 'first_news', $nuevos);

        return $nuevos;
    }

    private function intentarDesbloquear(int $userId, string $code, array &$nuevos): void
    {
        $logro = $this->unlock($userId, $code);
        if ($logro) $nuevos[] = $logro;
    }

    private function calcularRacha(int $userId): int
    {
        $registros = EmotionalRecord::where('user_id', $userId)
            ->select('recorded_at')
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn($r) => Carbon::parse($r->recorded_at)->startOfDay());

        $racha = 0;
        $hoy = Carbon::today();

        foreach ($registros as $i => $fecha) {
            if ($fecha->eq($hoy->copy()->subDays($i))) {
                $racha++;
            } else {
                break;
            }
        }

        return $racha;
    }
}