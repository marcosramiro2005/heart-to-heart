<?php

namespace App\Models;

// Tabla pivote entre usuarios y retos que incluye el progreso individual del usuario.
// A diferencia de una tabla pivote simple, tiene campos propios (progreso, fechas, estado).
// Estados posibles: 'active' (en curso), 'completed' (terminado), 'abandoned' (abandonado).

use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
    // Campos asignables:
    // - started_at: fecha en que el usuario se unió al reto
    // - completed_at: fecha en que terminó (null si no ha completado)
    // - current_day: número de día actual en el reto (equivale a count(completed_days))
    // - completed_days: array JSON con las fechas de los días que el usuario ha marcado como completados
    // - status: estado actual del reto ('active', 'completed', 'abandoned')
    protected $fillable = [
        'user_id', 'challenge_id', 'started_at',
        'completed_at', 'current_day', 'completed_days', 'status'
    ];

    // completed_days se guarda como JSON y se convierte a array PHP automáticamente
    protected $casts = [
        'started_at'     => 'date',
        'completed_at'   => 'date',
        'completed_days' => 'array',
    ];

    // Relación: este progreso corresponde a un reto específico del catálogo
    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    // Relación: este progreso pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calcula el porcentaje de progreso (días completados / duración total del reto × 100)
    public function progresoPorcentaje(): int
    {
        return (int) round(
            (count($this->completed_days) / $this->challenge->duration_days) * 100
        );
    }

    // Devuelve cuántos días quedan para completar el reto (mínimo 0, nunca negativo)
    public function diasRestantes(): int
    {
        return max(0, $this->challenge->duration_days - count($this->completed_days));
    }
}