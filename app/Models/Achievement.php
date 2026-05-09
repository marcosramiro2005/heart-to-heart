<?php

namespace App\Models;

// Modelo para los logros (achievements) de la aplicación.
// Representa los diferentes logros que los usuarios pueden desbloquear.

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'code', 'name', 'description',
        'emoji', 'color', 'category', 'points'
    ];

    // Relación muchos a muchos con usuarios (a través de user_achievements)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}