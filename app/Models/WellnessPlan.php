<?php

namespace App\Models;

// Modelo que representa el plan semanal de bienestar personalizado de un usuario.
// Cada plan cubre una semana (7 días) con una actividad distinta por día.
// El usuario puede tener un solo plan activo por semana; se genera uno nuevo cada lunes.

use Illuminate\Database\Eloquent\Model;

class WellnessPlan extends Model
{
    // Campos asignables:
    // - objetivo: clave del tipo de plan elegido (ansiedad, tristeza, estres, sueno, general)
    // - actividades: array JSON con las 7 actividades del plan (una por día de la semana)
    // - semana_inicio: fecha del lunes que inicia esa semana (ej: 2026-05-04)
    // - dias_completados: entero que cuenta cuántos días se han completado (0-7)
    // - dias_check: array JSON con las fechas (YYYY-MM-DD) de los días marcados como completados
    // - completado: true cuando dias_completados llega a 7
    protected $fillable = [
        'user_id', 'objetivo', 'actividades',
        'semana_inicio', 'dias_completados',
        'dias_check', 'completado',
    ];

    // actividades y dias_check se guardan como JSON y se convierten a array PHP automáticamente
    // semana_inicio se convierte a objeto Carbon date para poder formatear la fecha
    protected $casts = [
        'actividades'   => 'array',
        'dias_check'    => 'array',
        'semana_inicio' => 'date',
        'completado'    => 'boolean',
    ];

    // Relación: un plan de bienestar pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}