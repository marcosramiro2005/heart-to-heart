<?php

namespace App\Http\Controllers;

use App\Models\BreathingSession;
use Illuminate\Http\Request;

/**
 * BreathingSessionController
 *
 * Guarda las sesiones de respiración completadas por el usuario.
 * Después de cada sesión verifica si se han desbloqueado nuevos logros
 * relacionados con las técnicas de respiración.
 */
class BreathingSessionController extends Controller
{
    /**
     * Registra una sesión de respiración finalizada.
     * Recibe la técnica usada y la duración en minutos desde el frontend.
     * Tras guardar, comprueba si el usuario merece algún logro nuevo.
     */
    public function store(Request $request)
    {
        // Guardar la sesión con la técnica y duración indicadas
        BreathingSession::create([
            'user_id'          => auth()->id(),
            'technique'        => $request->technique,
            'duration_minutes' => $request->duration_minutes,
        ]);

        // Verificar logros relacionados con sesiones de respiración (ej: "primer_aliento", "5_sesiones")
        $achievementService = new \App\Services\AchievementService();
        $achievementService->verificarLogros(auth()->id());

        return response()->json(['message' => 'Sesión guardada']);
    }
}
