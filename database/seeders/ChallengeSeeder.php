<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $retos = [
            // ── 7 días ──
            [
                'title'         => 'Semana de respiración consciente',
                'description'   => 'Practica 5 minutos de respiración guiada cada día durante 7 días. Científicamente demostrado para reducir el cortisol.',
                'type'          => '7days',
                'category'      => 'ansiedad',
                'duration_days' => 7,
                'emoji'         => '🫁',
                'color'         => '#d4edda',
            ],
            [
                'title'         => 'Semana de gratitud',
                'description'   => 'Escribe 3 cosas por las que estás agradecido/a cada día durante 7 días. Transforma tu perspectiva.',
                'type'          => '7days',
                'category'      => 'autoestima',
                'duration_days' => 7,
                'emoji'         => '✨',
                'color'         => '#fff9c4',
            ],
            [
                'title'         => 'Semana sin pantallas por la noche',
                'description'   => 'Apaga todas las pantallas 60 minutos antes de dormir durante 7 días. Mejora tu sueño radicalmente.',
                'type'          => '7days',
                'category'      => 'sueno',
                'duration_days' => 7,
                'emoji'         => '🌙',
                'color'         => '#e8eaf6',
            ],
            [
                'title'         => 'Semana de movimiento',
                'description'   => 'Camina al menos 20 minutos cada día durante 7 días. El movimiento libera serotonina y dopamina.',
                'type'          => '7days',
                'category'      => 'ejercicio',
                'duration_days' => 7,
                'emoji'         => '🚶',
                'color'         => '#ffd5d5',
            ],
            [
                'title'         => 'Semana de mindfulness',
                'description'   => '5 minutos de meditación o atención plena cada día. Sin excusas, solo 5 minutos.',
                'type'          => '7days',
                'category'      => 'mindfulness',
                'duration_days' => 7,
                'emoji'         => '🧘',
                'color'         => '#e8d5f5',
            ],
            [
                'title'         => 'Semana de hidratación',
                'description'   => 'Bebe 8 vasos de agua al día durante 7 días. La deshidratación afecta directamente al estado de ánimo.',
                'type'          => '7days',
                'category'      => 'salud',
                'duration_days' => 7,
                'emoji'         => '💧',
                'color'         => '#d0eaf8',
            ],

            // ── 30 días ──
            [
                'title'         => 'Mes de bienestar integral',
                'description'   => '30 días construyendo hábitos de bienestar: una técnica diferente cada día, registro emocional diario y conexión con la comunidad.',
                'type'          => '30days',
                'category'      => 'general',
                'duration_days' => 30,
                'emoji'         => '🌱',
                'color'         => '#d4edda',
            ],
            [
                'title'         => 'Mes sin autocrítica',
                'description'   => '30 días practicando la autocompasión. Cada vez que te critiques, sustitúyelo por lo que le dirías a un amigo/a.',
                'type'          => '30days',
                'category'      => 'autoestima',
                'duration_days' => 30,
                'emoji'         => '💗',
                'color'         => '#fce4ec',
            ],
            [
                'title'         => 'Mes de conexión social',
                'description'   => '30 días conectando con personas que te importan. Un mensaje, una llamada o un encuentro cada día.',
                'type'          => '30days',
                'category'      => 'relaciones',
                'duration_days' => 30,
                'emoji'         => '🤝',
                'color'         => '#E8FAF9',
            ],
            [
                'title'         => 'Mes de sueño reparador',
                'description'   => '30 días construyendo una rutina de sueño: misma hora de acostarte, sin pantallas, técnica de relajación. Transforma tu descanso.',
                'type'          => '30days',
                'category'      => 'sueno',
                'duration_days' => 30,
                'emoji'         => '😴',
                'color'         => '#e8eaf6',
            ],
        ];

        foreach ($retos as $reto) {
            Challenge::updateOrCreate(['title' => $reto['title']], $reto);
        }
    }
}