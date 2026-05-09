<?php

namespace App\Models;

// Modelo que registra los "me gusta" de un usuario en un post del foro.
// Es una tabla de relación simple: un registro = un like de un usuario a un post.
// El ForumController gestiona el toggle: si ya existe se elimina, si no existe se crea.

use Illuminate\Database\Eloquent\Model;

class ForumLike extends Model
{
    // user_id: quien dio el like — forum_post_id: el post que recibió el like
    protected $fillable = ['user_id', 'forum_post_id'];
}