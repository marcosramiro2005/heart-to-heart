<?php

namespace App\Models;

// Modelo para los artículos y recursos de la Biblioteca de Bienestar.
// Los recursos son creados por el administrador mediante seeders y se muestran
// públicamente a todos los usuarios autenticados.

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    // Campos asignables:
    // - title / summary / content: título, resumen y contenido completo del artículo
    // - category: categoría (ansiedad, depresion, mindfulness, sueno, autoestima, etc.)
    // - type: formato del recurso (article, video, podcast, exercise)
    // - image_url: imagen de portada
    // - external_url: enlace externo si el contenido está alojado fuera
    // - read_time: tiempo estimado de lectura en minutos
    // - is_published: solo los recursos publicados son visibles en la biblioteca
    // - is_featured: los recursos destacados aparecen primero y en la sección superior
    // - views: contador de visitas (incrementado por el controlador al abrir el detalle)
    protected $fillable = [
        'title', 'content', 'summary', 'category',
        'type', 'image_url', 'external_url',
        'read_time', 'is_published', 'is_featured', 'views'
    ];

    // Convierte los campos booleanos para que se puedan usar directamente en Vue
    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
    ];

    // Relación: un recurso puede ser guardado por varios usuarios (ResourceSave = tabla pivote)
    public function saves()
    {
        return $this->hasMany(ResourceSave::class);
    }

    // Comprueba si un usuario específico ha guardado este recurso
    public function isSavedBy(int $userId): bool
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }

    // Accessor que devuelve el color de fondo asociado a la categoría del recurso.
    // Se usa en las tarjetas de la biblioteca para diferenciar categorías visualmente.
    // Se accede con $recurso->category_color
    public function getCategoryColorAttribute(): string
    {
        return [
            'ansiedad'    => '#d0eaf8',
            'depresion'   => '#e8d5f5',
            'mindfulness' => '#d4edda',
            'sueno'       => '#fff9c4',
            'autoestima'  => '#ffd5d5',
            'relaciones'  => '#E8FAF9',
            'alimentacion'=> '#ffe8cc',
            'ejercicio'   => '#d5f5e3',
        ][$this->category] ?? '#f0f0f0'; // color gris si la categoría no está en el mapa
    }
}