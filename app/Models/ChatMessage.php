<?php

namespace App\Models;

// Modelo que almacena cada mensaje intercambiado en el chat con el asistente Hearty.
// Cada fila es un mensaje individual: puede ser del usuario ('user') o del bot ('hearty').

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    // Campos asignables masivamente:
    // - user_id: el usuario dueño de la conversación
    // - sender: quién envió el mensaje, 'user' o 'hearty'
    // - message: el texto del mensaje
    // - emotion_detected: emoción que Flask detectó en ese mensaje (puede ser null)
    protected $fillable = [
        'user_id', 'sender', 'message', 'emotion_detected'
    ];

    // Convierte el campo recorded_at a objeto Carbon date
    protected $casts = [
        'recorded_at' => 'date',
    ];

    // Relación: un mensaje de chat pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}