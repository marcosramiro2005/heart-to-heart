<?php

namespace App\Models;

// Modelo que almacena cada registro emocional que el usuario hace desde el dashboard.
// Es el núcleo del sistema de seguimiento emocional de la aplicación.
// Cada registro captura qué emoción sintió el usuario, con qué intensidad y en qué contexto.

use Illuminate\Database\Eloquent\Model;

class EmotionalRecord extends Model
{
    // Campos asignables:
    // - emotion: nombre de la emoción (alegría, calma, ansiedad, tristeza, enfado, cansancio)
    // - intensity: nivel del 1 al 10 (cuánto afecta esa emoción)
    // - note: nota libre del usuario (campo principal)
    // - notes: campo legado (mismo propósito, columna más antigua)
    // - triggers: qué desencadenó la emoción (trabajo, familia, salud...)
    // - activities: qué estaba haciendo el usuario cuando lo sintió
    // - recorded_at: fecha del registro (permite registrar emociones de días anteriores)
    protected $fillable = [
        'user_id', 'emotion', 'intensity', 'notes', 'note', 'triggers', 'activities', 'recorded_at'
    ];

    // Convierte recorded_at a objeto Carbon para poder hacer comparaciones de fecha
    protected $casts = [
        'recorded_at' => 'date',
    ];

    // Relación: un registro emocional pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}