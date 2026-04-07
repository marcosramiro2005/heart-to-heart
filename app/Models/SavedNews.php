<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedNews extends Model
{
    protected $fillable = [
        'user_id', 'article_url', 'title',
        'description', 'image_url', 'source_name',
        'category', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}