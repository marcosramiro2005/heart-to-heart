<?php

namespace App\Models;

// Modelo que representa una sesión de ejercicio de respiración completada por un usuario.
// Se guarda automáticamente al terminar cualquier técnica de respiración desde la app.

use Illuminate\Database\Eloquent\Model;

class BreathingSession extends Model
{
    // Campos que se pueden asignar masivamente:
    // - user_id: usuario que hizo la sesión
    // - technique: nombre de la técnica usada (ej: '4-7-8', 'caja', 'diafragmatica')
    // - duration_minutes: duración total en minutos
    protected $fillable = [
        'user_id', 'technique', 'duration_minutes'
    ];

    // Convierte automáticamente el campo recorded_at a un objeto de fecha Carbon
    protected $casts = [
        'recorded_at' => 'date',
    ];

    // Relación: una sesión de respiración pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}