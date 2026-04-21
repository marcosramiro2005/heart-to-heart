<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellnessTest extends Model
{
    protected $fillable = [
        'user_id', 'puntuacion', 'nivel',
        'respuestas', 'recomendaciones'
    ];

    protected $casts = [
        'respuestas'      => 'array',
        'recomendaciones' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function interpretarPuntuacion(int $puntos): array
    {
        if ($puntos <= 4) {
            return [
                'nivel'       => 'minimo',
                'titulo'      => 'Bienestar mínimo afectado',
                'color'       => '#d4edda',
                'emoji'       => '😊',
                'descripcion' => 'Tu bienestar emocional está en buen estado. Sigue manteniendo tus hábitos saludables.',
            ];
        }

        if ($puntos <= 9) {
            return [
                'nivel'       => 'leve',
                'titulo'      => 'Malestar leve',
                'color'       => '#fff9c4',
                'emoji'       => '😌',
                'descripcion' => 'Hay algunos aspectos de tu bienestar que merecen atención. Las técnicas de la app pueden ayudarte.',
            ];
        }

        if ($puntos <= 14) {
            return [
                'nivel'       => 'moderado',
                'titulo'      => 'Malestar moderado',
                'color'       => '#ffecd2',
                'emoji'       => '😕',
                'descripcion' => 'Estás experimentando un nivel de malestar que merece atención. Considera hablar con un profesional.',
            ];
        }

        if ($puntos <= 19) {
            return [
                'nivel'       => 'moderado_severo',
                'titulo'      => 'Malestar moderado-severo',
                'color'       => '#ffd5d5',
                'emoji'       => '😔',
                'descripcion' => 'Tu puntuación indica un nivel importante de malestar. Te recomendamos buscar apoyo profesional.',
            ];
        }

        return [
            'nivel'       => 'severo',
            'titulo'      => 'Malestar severo',
            'color'       => '#ffb3b3',
            'emoji'       => '💙',
            'descripcion' => 'Tu puntuación es elevada. Por favor busca apoyo profesional. Llama al 024 si lo necesitas.',
        ];
    }
}