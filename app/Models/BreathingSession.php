<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreathingSession extends Model
{
    protected $fillable = [
        'user_id', 'technique', 'duration_minutes'
    ];

    protected $casts = [
        'recorded_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}