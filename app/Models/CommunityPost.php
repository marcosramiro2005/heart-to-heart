<?php

namespace App\Models;

// Modelo para los posts de la comunidad (funcionalidad anterior al foro).
// El foro más completo utiliza ForumPost; este modelo es el sistema original más simple.

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    // Campos asignables: usuario, título, contenido, likes y si es anónimo
    protected $fillable = [
        'user_id', 'title', 'content', 'likes', 'is_anonymous'
    ];

    // Convierte recorded_at a objeto de fecha Carbon
    protected $casts = [
        'recorded_at' => 'date',
    ];

    // Relación: un post de comunidad pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}