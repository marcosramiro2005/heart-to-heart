<?php

namespace App\Http\Controllers;

use App\Models\WellnessTest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WellnessTestController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $historial = WellnessTest::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(8)
            ->get()
            ->map(fn($t) => [
                'id'          => $t->id,
                'puntuacion'  => $t->puntuacion,
                'nivel'       => $t->nivel,
                'fecha'       => $t->created_at->format('d/m/Y'),
                'dias'        => $t->created_at->diffInDays(now()),
                'interpretacion' => WellnessTest::interpretarPuntuacion($t->puntuacion),
            ]);

        $ultimo = $historial->first();
        $puedeHacerTest = !$ultimo || $ultimo['dias'] >= 7;

        return Inertia::render('WellnessTest/Index', [
            'historial'        => $historial,
            'ultimo'           => $ultimo,
            'puedeHacerTest'   => $puedeHacerTest,
        ]);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'respuestas'  => 'required|array|size:9',
            'puntuacion'  => 'required|integer|min:0|max:27',
        ]);

        $interpretacion   = WellnessTest::interpretarPuntuacion($request->puntuacion);
        $recomendaciones  = $this->generarRecomendaciones(
            $request->puntuacion,
            $interpretacion['nivel'],
            $request->respuestas
        );

        $test = WellnessTest::create([
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

    private function generarRecomendaciones(int $puntos, string $nivel, array $respuestas): array
    {
        $recs = [];

        // Basadas en puntuación global
        if ($puntos <= 4) {
            $recs[] = ['tipo' => 'positivo', 'texto' => 'Mantén tus hábitos actuales. Estás haciendo bien las cosas.'];
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'El diario de gratitud puede ayudarte a mantener este bienestar.', 'ruta' => '/diario'];
        } elseif ($puntos <= 9) {
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'La respiración guiada diaria puede mejorar tu estado de ánimo.', 'ruta' => '/respiracion'];
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'Prueba un reto de 7 días de bienestar para crear hábitos.', 'ruta' => '/retos'];
        } elseif ($puntos <= 14) {
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'Habla con Hearty cada día esta semana.', 'ruta' => '/hearty'];
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'El ejercicio terapéutico puede mejorar tu estado de ánimo significativamente.', 'ruta' => '/ejercicio'];
            $recs[] = ['tipo' => 'profesional', 'texto' => 'Considera hablar con tu médico o un psicólogo.'];
        } else {
            $recs[] = ['tipo' => 'urgente',  'texto' => 'Tu puntuación indica que mereces apoyo profesional. No estás solo/a.'];
            $recs[] = ['tipo' => 'recurso',  'texto' => 'Línea de atención psicológica 024 — gratuita y disponible 24h.'];
            $recs[] = ['tipo' => 'tecnica',  'texto' => 'El modo SOS tiene técnicas de emergencia para ahora mismo.', 'ruta' => '/sos'];
        }

        // Basadas en preguntas específicas
        if (($respuestas[2] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para los problemas de sueño: infusiones de valeriana y rutina nocturna.', 'ruta' => '/infusiones'];
        }

        if (($respuestas[3] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para el cansancio: yoga suave y técnicas de relajación muscular.', 'ruta' => '/yoga'];
        }

        if (($respuestas[4] ?? 0) >= 2) {
            $recs[] = ['tipo' => 'tecnica', 'texto' => 'Para el apetito: el diario puede ayudarte a identificar patrones emocionales.', 'ruta' => '/diario'];
        }

        return array_slice($recs, 0, 5);
    }
}