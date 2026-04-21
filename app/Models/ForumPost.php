<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = [
    'user_id', 'title', 'content', 'is_anonymous',
    'categoria', 'category', 'is_featured', 'views',
    'likes_count', 'comments_count',
];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class)->orderBy('created_at', 'asc');
    }

    public function likes()
    {
        return $this->hasMany(ForumLike::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    // Nombre a mostrar según anonimato
    public function getAuthorNameAttribute()
    {
        return $this->is_anonymous ? 'Usuario anónimo' : $this->user->name;
    }
}