<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $logros = [
            // ── Rachas ──
            [
                'code'        => 'first_day',
                'name'        => 'Primer paso',
                'description' => 'Registraste tu primera emoción del día',
                'emoji'       => '🌱',
                'color'       => '#4ECDC4',
                'category'    => 'racha',
                'points'      => 10,
            ],
            [
                'code'        => 'streak_3',
                'name'        => 'Tres días seguidos',
                'description' => 'Registraste tu emoción 3 días consecutivos',
                'emoji'       => '⭐',
                'color'       => '#FFD700',
                'category'    => 'racha',
                'points'      => 20,
            ],
            [
                'code'        => 'streak_7',
                'name'        => 'Una semana completa',
                'description' => 'Registraste tu emoción 7 días seguidos',
                'emoji'       => '🔥',
                'color'       => '#FF8C42',
                'category'    => 'racha',
                'points'      => 50,
            ],
            [
                'code'        => 'streak_30',
                'name'        => 'Mes de bienestar',
                'description' => '30 días consecutivos cuidando tus emociones',
                'emoji'       => '🏆',
                'color'       => '#E63946',
                'category'    => 'racha',
                'points'      => 200,
            ],

            // ── Bienestar ──
            [
                'code'        => 'first_breath',
                'name'        => 'Primera respiración',
                'description' => 'Completaste tu primera sesión de respiración',
                'emoji'       => '🫁',
                'color'       => '#d4edda',
                'category'    => 'bienestar',
                'points'      => 15,
            ],
            [
                'code'        => 'breath_5',
                'name'        => 'Respiración constante',
                'description' => 'Completaste 5 sesiones de respiración',
                'emoji'       => '💨',
                'color'       => '#4ECDC4',
                'category'    => 'bienestar',
                'points'      => 30,
            ],
            [
                'code'        => 'first_diary',
                'name'        => 'Diario abierto',
                'description' => 'Escribiste tu primera entrada en el diario',
                'emoji'       => '📓',
                'color'       => '#fff9c4',
                'category'    => 'bienestar',
                'points'      => 15,
            ],
            [
                'code'        => 'all_techniques',
                'name'        => 'Explorador del bienestar',
                'description' => 'Probaste todas las técnicas disponibles',
                'emoji'       => '🌟',
                'color'       => '#9B8EC4',
                'category'    => 'bienestar',
                'points'      => 75,
            ],

            // ── Social ──
            [
                'code'        => 'first_post',
                'name'        => 'Voz propia',
                'description' => 'Publicaste tu primera experiencia en la comunidad',
                'emoji'       => '💬',
                'color'       => '#6B9FD4',
                'category'    => 'social',
                'points'      => 20,
            ],
            [
                'code'        => 'first_comment',
                'name'        => 'Apoyo mutuo',
                'description' => 'Comentaste en la experiencia de otra persona',
                'emoji'       => '🤝',
                'color'       => '#4ECDC4',
                'category'    => 'social',
                'points'      => 15,
            ],
            [
                'code'        => 'posts_5',
                'name'        => 'Voz activa',
                'description' => 'Publicaste 5 experiencias en la comunidad',
                'emoji'       => '📢',
                'color'       => '#FF8C42',
                'category'    => 'social',
                'points'      => 40,
            ],

            // ── Explorador ──
            [
                'code'        => 'first_news',
                'name'        => 'Bien informado',
                'description' => 'Guardaste tu primer artículo de recursos',
                'emoji'       => '📰',
                'color'       => '#d0eaf8',
                'category'    => 'explorador',
                'points'      => 10,
            ],
            [
                'code'        => 'hearty_chat',
                'name'        => 'Abriendo el corazón',
                'description' => 'Tuviste tu primera conversación completa con Hearty',
                'emoji'       => '💚',
                'color'       => '#4ECDC4',
                'category'    => 'explorador',
                'points'      => 25,
            ],
            [
                'code'        => 'emotions_10',
                'name'        => 'Autoconocimiento',
                'description' => 'Registraste 10 emociones en tu diario',
                'emoji'       => '🧠',
                'color'       => '#e8d5f5',
                'category'    => 'explorador',
                'points'      => 50,
            ],
        ];

        foreach ($logros as $logro) {
            Achievement::updateOrCreate(['code' => $logro['code']], $logro);
        }
    }
}