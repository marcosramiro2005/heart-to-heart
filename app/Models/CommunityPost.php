<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'content', 'likes', 'is_anonymous'
    ];

    protected $casts = [
        'recorded_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}