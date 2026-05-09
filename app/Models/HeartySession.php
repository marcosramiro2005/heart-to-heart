<?php

namespace App\Models;

// Modelo que guarda el perfil emocional acumulado de un usuario en sus conversaciones con Hearty.
// Cada usuario tiene UNA sola fila en esta tabla (se usa firstOrCreate en el controlador).
// Se actualiza con cada mensaje procesado para mantener el historial emocional al día.

use Illuminate\Database\Eloquent\Model;

class HeartySession extends Model
{
    // Campos asignables:
    // - dominant_emotion: la emoción que más veces ha aparecido en el historial
    // - avg_intensity: media de intensidad de todas las emociones detectadas
    // - emotions_history: array JSON con hasta 20 entradas {emocion, intensidad, fecha}
    // - suggested_techniques: técnicas que Flask ha recomendado en las últimas sesiones
    // - session_count: cuántas veces el usuario ha abierto el chat de Hearty
    // - last_summary: frase descriptiva del patrón emocional reciente
    // - last_session_at: timestamp de la última conversación
    protected $fillable = [
        'user_id', 'dominant_emotion', 'avg_intensity',
        'emotions_history', 'suggested_techniques',
        'session_count', 'last_summary', 'last_session_at'
    ];

    // emotions_history y suggested_techniques se guardan como JSON en la BD
    // y Laravel los convierte automáticamente a arrays PHP al leerlos
    protected $casts = [
        'emotions_history'    => 'array',
        'suggested_techniques'=> 'array',
        'avg_intensity'       => 'float',
        'last_session_at'     => 'datetime',
    ];

    // Relación: una sesión de Hearty pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}