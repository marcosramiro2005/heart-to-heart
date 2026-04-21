<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellnessPlan extends Model
{
    protected $fillable = [
        'user_id', 'objetivo', 'actividades',
        'semana_inicio', 'dias_completados',
        'dias_check', 'completado',
    ];

    protected $casts = [
        'actividades'    => 'array',
        'dias_check'     => 'array',
        'semana_inicio'  => 'date',
        'completado'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}