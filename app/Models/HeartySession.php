<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeartySession extends Model
{
    protected $fillable = [
        'user_id', 'dominant_emotion', 'avg_intensity',
        'emotions_history', 'suggested_techniques',
        'session_count', 'last_summary', 'last_session_at'
    ];

    protected $casts = [
        'emotions_history'    => 'array',
        'suggested_techniques'=> 'array',
        'avg_intensity'       => 'float',
        'last_session_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}