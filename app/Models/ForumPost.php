<?php

namespace App\Models;

// Modelo principal del foro comunitario.
// Representa una publicación creada por un usuario, que puede ser anónima,
// tener comentarios, likes y ser marcada como destacada por el admin.
//
// NOTA: Las columnas 'categoria' y 'category' son redundantes.
// 'category' es la columna original; 'categoria' se añadió después.
// Ambas se guardan simultáneamente en el controlador para retrocompatibilidad.

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    // Campos asignables:
    // - categoria / category: categoría del post (general, ansiedad, depresion, etc.)
    // - is_featured: si el admin lo ha marcado como destacado (aparece en el panel lateral)
    // - views / likes_count / comments_count: contadores desnormalizados para evitar queries pesadas
    protected $fillable = [
        'user_id', 'title', 'content', 'is_anonymous',
        'categoria', 'category', 'is_featured', 'views',
        'likes_count', 'comments_count',
    ];

    // Convierte booleanos para usarlos directamente en Vue
    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_pinned'    => 'boolean',
    ];

    // Relación: un post pertenece a un usuario (el autor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: un post tiene muchos comentarios, ordenados del más antiguo al más nuevo
    public function comments()
    {
        return $this->hasMany(ForumComment::class)->orderBy('created_at', 'asc');
    }

    // Relación: un post tiene muchos likes (tabla forum_likes)
    public function likes()
    {
        return $this->hasMany(ForumLike::class);
    }

    // Comprueba si un usuario específico ya ha dado like a este post
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    // Accessor que oculta el nombre real del autor si el post fue publicado como anónimo
    public function getAuthorNameAttribute()
    {
        return $this->is_anonymous ? 'Usuario anónimo' : $this->user->name;
    }
}