<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    protected $fillable = [
        'user_id', 'content', 'mood',
        'mood_score', 'tags', 'is_private', 'weather'
    ];

    protected $casts = [
        'tags'       => 'array',
        'is_private' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}