<?php

namespace App\Models;

// Modelo que representa una entrada en el diario personal del usuario.
// El diario es privado por defecto; cada entrada puede tener un estado de ánimo,
// una puntuación numérica del mood, etiquetas y anotación del clima.

use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    // Campos asignables:
    // - content: el texto de la entrada (mínimo 10 caracteres)
    // - mood: nombre del estado de ánimo (ej: 'feliz', 'triste', 'ansioso')
    // - mood_score: puntuación del 1 al 10 para la gráfica de evolución
    // - tags: array de etiquetas opcionales (familia, trabajo, etc.)
    // - is_private: si la entrada es privada (de momento siempre lo es)
    // - weather: clima del día (opcional, no implementado en la UI actual)
    protected $fillable = [
        'user_id', 'content', 'mood',
        'mood_score', 'tags', 'is_private', 'weather'
    ];

    // tags se guarda como JSON en la BD y Laravel lo convierte automáticamente a array PHP
    // is_private se convierte a boolean para usarlo como true/false en Vue
    protected $casts = [
        'tags'       => 'array',
        'is_private' => 'boolean',
    ];

    // Relación: una entrada del diario pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}