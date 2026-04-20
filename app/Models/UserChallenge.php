<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserChallenge extends Model
{
    protected $fillable = [
        'user_id', 'challenge_id', 'started_at',
        'completed_at', 'current_day', 'completed_days', 'status'
    ];

    protected $casts = [
        'started_at'     => 'date',
        'completed_at'   => 'date',
        'completed_days' => 'array',
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progresoPorcentaje(): int
    {
        return (int) round(
            (count($this->completed_days) / $this->challenge->duration_days) * 100
        );
    }

    public function diasRestantes(): int
    {
        return max(0, $this->challenge->duration_days - count($this->completed_days));
    }
}