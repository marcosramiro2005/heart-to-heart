<?php

namespace App\Models;

// Tabla pivote entre usuarios y logros: registra qué logros ha desbloqueado cada usuario y cuándo.
// Esta tabla es creada por AchievementService::unlock() cuando se cumplen las condiciones de un logro.
// Se puede acceder a ella también desde User::achievements() que usa belongsToMany con withPivot.

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    // user_id: usuario que desbloqueó el logro
    // achievement_id: el logro desbloqueado
    // unlocked_at: fecha y hora exacta del desbloqueo (se muestra en el perfil del usuario)
    protected $fillable = ['user_id', 'achievement_id', 'unlocked_at'];

    // Convierte unlocked_at a objeto Carbon datetime para formatear la fecha en la vista
    protected $casts = [
        'unlocked_at' => 'datetime',
    ];
}