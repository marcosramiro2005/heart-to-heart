<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Services\AchievementService;
use Inertia\Inertia;

/**
 * AchievementController
 *
 * Gestiona el sistema de logros de la aplicación:
 * - Muestra todos los logros (bloqueados y desbloqueados) del usuario
 * - Calcula el nivel actual basado en los puntos acumulados
 * - Permite verificar manualmente si hay nuevos logros disponibles
 */
class AchievementController extends Controller
{
    /**
     * Inyección del servicio de logros mediante el constructor.
     * Laravel resuelve automáticamente la dependencia.
     */
    public function __construct(private AchievementService $service) {}

    /**
     * Muestra la página de logros con todos los logros del sistema
     * marcando cuáles ha desbloqueado el usuario y su nivel actual.
     */
    public function index()
    {
        $userId = auth()->id();

        // Obtener todos los logros del sistema, ordenados por categoría y puntos
        $todosLosLogros = Achievement::orderBy('category')
            ->orderBy('points')
            ->get();

        // IDs de los logros que el usuario ya ha desbloqueado
        $desbloqueados = auth()->user()
            ->achievements()
            ->pluck('achievement_id')
            ->toArray();

        // Mapear cada logro añadiendo el campo 'unlocked' según si el usuario lo tiene
        $logros = $todosLosLogros->map(fn($logro) => [
            'id'          => $logro->id,
            'code'        => $logro->code,
            'name'        => $logro->name,
            'description' => $logro->description,
            'emoji'       => $logro->emoji,
            'color'       => $logro->color,
            'category'    => $logro->category,
            'points'      => $logro->points,
            'unlocked'    => in_array($logro->id, $desbloqueados),
        ]);

        // Calcular puntos totales y nivel del usuario
        $puntosTotales = auth()->user()->totalPoints();
        $nivel = $this->calcularNivel($puntosTotales);

        return Inertia::render('Achievements/Index', [
            'logros'        => $logros,
            'puntosTotales' => $puntosTotales,
            'nivel'         => $nivel,
            'totalUnlocked' => count($desbloqueados),
            'totalLogros'   => $todosLosLogros->count(),
        ]);
    }

    /**
     * Verifica si el usuario ha desbloqueado nuevos logros y los devuelve como JSON.
     * Se llama desde el frontend después de realizar ciertas acciones.
     */
    public function verificar()
    {
        $nuevos = $this->service->verificarLogros(auth()->id());

        return response()->json([
            'nuevos_logros' => collect($nuevos)->map(fn($l) => [
                'name'  => $l->name,
                'emoji' => $l->emoji,
                'points'=> $l->points,
            ])
        ]);
    }

    /**
     * Calcula el nivel actual del usuario según sus puntos totales.
     * Devuelve información del nivel actual, progreso hacia el siguiente y nombre del siguiente nivel.
     *
     * @param  int  $puntos  Puntos totales acumulados por el usuario
     * @return array  Datos del nivel: nombre, emoji, color, porcentaje de progreso y nivel siguiente
     */
    private function calcularNivel(int $puntos): array
    {
        // Definición de todos los niveles con su rango de puntos
        $niveles = [
            ['nombre' => 'Semilla',  'min' => 0,    'max' => 49,         'emoji' => '🌱', 'color' => '#4ECDC4'],
            ['nombre' => 'Brote',    'min' => 50,   'max' => 149,        'emoji' => '🌿', 'color' => '#45B7B0'],
            ['nombre' => 'Flor',     'min' => 150,  'max' => 299,        'emoji' => '🌸', 'color' => '#FF8C42'],
            ['nombre' => 'Árbol',    'min' => 300,  'max' => 599,        'emoji' => '🌳', 'color' => '#3BAFA7'],
            ['nombre' => 'Guardián', 'min' => 600,  'max' => 999,        'emoji' => '⭐', 'color' => '#FFD700'],
            ['nombre' => 'Maestro',  'min' => 1000, 'max' => PHP_INT_MAX,'emoji' => '🏆', 'color' => '#E63946'],
        ];

        // El nivel actual es el último cuyo mínimo no supera los puntos del usuario
        $nivelActual = collect($niveles)->last(fn($n) => $puntos >= $n['min']);
        // El siguiente nivel es el primero cuyo mínimo aún no se ha alcanzado
        $siguiente   = collect($niveles)->first(fn($n) => $puntos < $n['min']);

        return [
            'nombre'   => $nivelActual['nombre'],
            'emoji'    => $nivelActual['emoji'],
            'color'    => $nivelActual['color'],
            // Porcentaje de progreso dentro del rango del nivel actual
            'progreso' => $siguiente
                ? round((($puntos - $nivelActual['min']) / ($siguiente['min'] - $nivelActual['min'])) * 100)
                : 100, // Si ya está en el nivel máximo, progreso = 100%
            'siguiente'        => $siguiente['nombre'] ?? 'Nivel máximo',
            'puntos_siguiente' => $siguiente['min'] ?? $puntos,
        ];
    }
}