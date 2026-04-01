<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'title', 'content', 'category', 'image_url', 'is_published'
    ];

    protected $casts = [
        'recorded_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}