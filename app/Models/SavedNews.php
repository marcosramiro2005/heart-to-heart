<?php

namespace App\Models;

// Modelo para los artículos de noticias que el usuario ha guardado desde la sección /recursos.
// Los artículos provienen de la API externa (NewsAPI) y se guardan localmente con sus metadatos
// para que el usuario pueda releerlos aunque la API ya no los devuelva.

use Illuminate\Database\Eloquent\Model;

class SavedNews extends Model
{
    // Campos asignables:
    // - article_url: URL única del artículo (se usa como identificador para el toggle guardar/desguardar)
    // - title / description / image_url / source_name: metadatos del artículo copiados de la API
    // - category: categoría bajo la que se guardó (salud_mental, ansiedad, etc.)
    // - published_at: fecha de publicación original del artículo
    protected $fillable = [
        'user_id', 'article_url', 'title',
        'description', 'image_url', 'source_name',
        'category', 'published_at'
    ];

    // Convierte published_at a objeto Carbon datetime para formateo de fecha
    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Relación: un artículo guardado pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}