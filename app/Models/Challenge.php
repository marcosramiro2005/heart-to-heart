<?php

namespace App\Models;

// Modelo que representa un reto de bienestar disponible en la aplicación.
// Los retos son creados por el administrador mediante el seeder y tienen una duración en días.
// Los usuarios se apuntan a ellos a través del modelo UserChallenge.

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    // Campos asignables masivamente:
    // - title: nombre del reto (ej: "7 días de meditación")
    // - description: explicación del reto
    // - type: tipo (habit, mindfulness, exercise...)
    // - category: categoría (ansiedad, sueño, bienestar...)
    // - duration_days: cuántos días dura el reto
    // - emoji/color: para la interfaz visual
    protected $fillable = [
        'title', 'description', 'type', 'category',
        'duration_days', 'emoji', 'color'
    ];

    // Relación: un reto puede tener muchos usuarios apuntados (UserChallenge = tabla pivote con extras)
    public function userChallenges()
    {
        return $this->hasMany(UserChallenge::class);
    }

    // Comprueba rápidamente si un usuario específico está participando activamente en este reto
    public function isActiveForUser(int $userId): bool
    {
        return $this->userChallenges()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
    }
}