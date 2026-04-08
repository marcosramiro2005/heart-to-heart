<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'title', 'content', 'summary', 'category',
        'type', 'image_url', 'external_url',
        'read_time', 'is_published', 'is_featured', 'views'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
    ];

    public function saves()
    {
        return $this->hasMany(ResourceSave::class);
    }

    public function isSavedBy(int $userId): bool
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }

    public function getCategoryColorAttribute(): string
    {
        return [
            'ansiedad'   => '#d0eaf8',
            'depresion'  => '#e8d5f5',
            'mindfulness'=> '#d4edda',
            'sueno'      => '#fff9c4',
            'autoestima' => '#ffd5d5',
            'relaciones' => '#E8FAF9',
            'alimentacion'=> '#ffe8cc',
            'ejercicio'  => '#d5f5e3',
        ][$this->category] ?? '#f0f0f0';
    }
}