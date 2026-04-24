<?php

namespace App\Http\Controllers;

use App\Models\WellnessTest;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * WellnessTestController
 *
 * Gestiona el test de bienestar emocional (basado en el PHQ-9 simplificado):
 * - Muestra el historial de los últimos 8 tests y controla si el usuario puede hacer uno nuevo
 * - Guarda el resultado del test junto con recomendaciones personalizadas
 * - Limita la frecuencia: solo se puede hacer un test cada 7 días
 *
 * El test tiene 9 preguntas con respuestas de 0 a 3, dando una puntuación de 0 a 27.
 */
class WellnessTestController extends Controller
{
    /**
     * Muestra la página del test de bienestar con el historial de resultados.
     * Calcula si el usuario puede hacer un nuevo test (mínimo 7 días desde el último).
     */
    public function index()
    {
        $userId   = auth()->id();

        // Últimos 8 tests para mostrar la evolución temporal
        $historial = WellnessTest::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(8)
            ->get()
            ->map(fn($t) => [
                'id'             => $t->id,
                'puntuacion'     => $t->puntuacion,
                'nivel'          => $t->nivel,
                'fecha'          => $t->created_at->format('d/m/Y'),
                // Días desde el test (para calcular si puede hacer uno nuevo)
                'dias'           => $t->created_at->diffInDays(now()),
                // Interpretación de la puntuación (nivel, color, emoji y descripción)
                'interpretacion' => WellnessTest::interpretarPuntuacion($t->puntuacion),
            ]);

        $ultimo = $historial->first();
        // El usuario puede hacer test si nunca ha hecho uno, o si el último fue hace ≥7 días
        $puedeHacerTest = !$ultimo || $ultimo['dias'] >= 7;

        return Inertia::render('WellnessTest/Index', [
            'historial'      => $historial,
            'ultimo'         => $ultimo,
            'puedeHacerTest' => $puedeHacerTest,
        ]);
    }

    /**
     * Guarda el resultado de un test completado y devuelve la interpretación y recomendaciones.
     * Recibe las 9 respuestas (0-3 cada una) y la puntuación total (0-27).
     * Responde con JSON para mostrar los resultados en un modal sin recargar la página.
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'respuestas' => 'required|array|size:9',  // Exactamente 9 respuestas
            'puntuacion' => 'required|integer|min:0|max:27',
        ]);

        // Interpretar la puntuación usando el método estático del modelo
        $interpretacion  = WellnessTest::interpretarPuntuacion($request->puntuacion);
        // Generar recomendaciones personalizadas basadas en puntuación y respuestas concretas
        $recomendaciones = $this->generarRecomendaciones(
            $request->puntuacion,
            $interpretacion['nivel'],
            $request->respuestas
        );

        WellnessTest::create([
            'user_id'        => auth()->id(),
            'puntuacion'     => $request->puntuacion,
            'nivel'          => $interpretacion['nivel'],
            'respuestas'     => $request->respuestas,
            'recomendaciones'=> $recomendaciones,
        ]);

        return response()->json([
            'interpretacion'  => $interpretacion,
            'recomendaciones' => $recomendaciones,
            'puntuacion'      => $request->puntuacion,
        ]);
    }

    /**
     * Genera hasta 5 recomendaciones personalizadas combinando:
     * - La puntuación global (tramos: mínimo, leve, moderado, severo)
     * - Las respuestas a preguntas específicas (sueño, cansancio, apetito)
     *
     * @param  int    $puntos     Puntuación total del test (0-27)
     * @param  string $nivel      Nivel interpretado: 'minimo', 'leve', 'moderado', 'severo'
     * @param  array  $respuestas Array de 9 respuestas (0-3 cada una)
     * @return array  Lista de recomendaciones con 'tipo', 'texto' y opcionalmente 'ruta'
     */
    private function generarRecomendaciones(int $puntos, string $nivel, array $respuestas): array
    {
        $recs = [];

        // Recomendaciones según el nivel global de bienestar
        if ($puntos <= 4) {
            // Nivel mínimo: el usuario está bien, reforzar hábitos positivos
            $recs[] = ['tipo' => 'positivo', 'texto' => 'Mantén tus hábitos actuales. Estás haciendo bien las cosas.'];
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'El diario de gratitud puede ayudarte a mantener este bienestar.', 'ruta' => '/diario'];
        } elseif ($puntos <= 9) {
            // Nivel leve: pequeñas mejoras pueden hacer gran diferencia
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'La respiración guiada diaria puede mejorar tu estado de ánimo.', 'ruta' => '/respiracion'];
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Prueba un reto de 7 días de bienestar para crear hábitos.', 'ruta' => '/retos'];
        } elseif ($puntos <= 14) {
            // Nivel moderado: se recomienda apoyo profesional
            $recs[] = ['tipo' => 'tecnica',     'texto' => 'Habla con Hearty cada día esta semana.', 'ruta' => '/hearty'];
            $recs[] = ['tipo' => 'tecnica',     'texto' => 'El ejercicio terapéutico puede mejorar tu estado de ánimo significativamente.', 'ruta' => '/ejercicio'];
            $recs[] = ['tipo' => 'profesional', 'texto' => 'Considera hablar con tu médico o un psicólogo.'];
        } else {
            // Nivel severo: se insiste en buscar apoyo profesional urgente
            $recs[] = ['tipo' => 'urgente', 'texto' => 'Tu puntuación indica que mereces apoyo profesional. No estás solo/a.'];
            $recs[] = ['tipo' => 'recurso', 'texto' => 'Línea de atención psicológica 024 — gratuita y disponible 24h.'];
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'El modo SOS tiene técnicas de emergencia para ahora mismo.', 'ruta' => '/sos'];
        }

        // Recomendaciones adicionales basadas en preguntas específicas del test:
        // Pregunta 3 (índice 2): problemas de sueño
        if (($respuestas[2] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para los problemas de sueño: infusiones de valeriana y rutina nocturna.', 'ruta' => '/infusiones'];
        }

        // Pregunta 4 (índice 3): cansancio o falta de energía
        if (($respuestas[3] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para el cansancio: yoga suave y técnicas de relajación muscular.', 'ruta' => '/yoga'];
        }

        // Pregunta 5 (índice 4): cambios en el apetito
        if (($respuestas[4] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para el apetito: el diario puede ayudarte a identificar patrones emocionales.', 'ruta' => '/diario'];
        }

        // Limitar a 5 recomendaciones para no saturar la vista
        return array_slice($recs, 0, 5);
    }
}