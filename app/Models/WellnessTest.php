<?php

namespace App\Models;

// Modelo que almacena los resultados del test de bienestar emocional (PHQ-9 simplificado).
// El test tiene 9 preguntas con respuestas de 0 a 3, dando una puntuación total de 0 a 27.
// Solo se puede hacer un test cada 7 días para medir la evolución a lo largo del tiempo.

use Illuminate\Database\Eloquent\Model;

class WellnessTest extends Model
{
    // Campos asignables:
    // - puntuacion: suma total de las 9 respuestas (0-27)
    // - nivel: cadena interpretativa del resultado ('minimo', 'leve', 'moderado', 'moderado_severo', 'severo')
    // - respuestas: array JSON con las 9 respuestas individuales del usuario (0-3 cada una)
    // - recomendaciones: array JSON con hasta 5 sugerencias personalizadas generadas por el controlador
    protected $fillable = [
        'user_id', 'puntuacion', 'nivel',
        'respuestas', 'recomendaciones'
    ];

    // Convierte los arrays JSON a arrays PHP automáticamente al leer el modelo
    protected $casts = [
        'respuestas'      => 'array',
        'recomendaciones' => 'array',
    ];

    // Relación: un test de bienestar pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método estático que convierte una puntuación numérica en datos interpretables para la UI.
    // Devuelve nivel, título, color de fondo, emoji y descripción textual.
    // Se llama tanto al guardar el test (controlador) como al mostrar el historial.
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