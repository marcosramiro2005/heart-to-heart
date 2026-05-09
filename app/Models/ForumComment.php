<?php

namespace App\Models;

// Modelo que representa un comentario dentro de un post del foro.
// Un comentario puede publicarse de forma anónima para proteger la privacidad del usuario.

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    // Campos asignables:
    // - user_id: autor del comentario (siempre se guarda aunque sea anónimo, por seguridad)
    // - forum_post_id: post al que pertenece el comentario
    // - content: texto del comentario
    // - is_anonymous: si true, se muestra como "Usuario anónimo" en la vista
    protected $fillable = [
        'user_id', 'forum_post_id', 'content', 'is_anonymous'
    ];

    // Convierte is_anonymous a boolean para usarlo directamente en Vue como true/false
    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    // Relación: un comentario pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor que devuelve el nombre del autor según si el comentario es anónimo o no.
    // Se usa con $comentario->author_name en vez de lógica condicional en la vista.
    public function getAuthorNameAttribute()
    {
        return $this->is_anonymous ? 'Usuario anónimo' : $this->user->name;
    }
}