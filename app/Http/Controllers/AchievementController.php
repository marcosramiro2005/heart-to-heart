<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Services\AchievementService;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function __construct(private AchievementService $service) {}

    public function index()
    {
        $userId = auth()->id();

        $todosLosLogros = Achievement::orderBy('category')
            ->orderBy('points')
            ->get();

        $desbloqueados = auth()->user()
            ->achievements()
            ->pluck('achievement_id')
            ->toArray();

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

    private function calcularNivel(int $puntos): array
    {
        $niveles = [
            ['nombre' => 'Semilla',      'min' => 0,   'max' => 49,  'emoji' => '🌱', 'color' => '#4ECDC4'],
            ['nombre' => 'Brote',        'min' => 50,  'max' => 149, 'emoji' => '🌿', 'color' => '#45B7B0'],
            ['nombre' => 'Flor',         'min' => 150, 'max' => 299, 'emoji' => '🌸', 'color' => '#FF8C42'],
            ['nombre' => 'Árbol',        'min' => 300, 'max' => 599, 'emoji' => '🌳', 'color' => '#3BAFA7'],
            ['nombre' => 'Guardián',     'min' => 600, 'max' => 999, 'emoji' => '⭐', 'color' => '#FFD700'],
            ['nombre' => 'Maestro',      'min' => 1000,'max' => PHP_INT_MAX,'emoji' => '🏆','color' => '#E63946'],
        ];

        $nivelActual = collect($niveles)->last(fn($n) => $puntos >= $n['min']);
        $siguiente   = collect($niveles)->first(fn($n) => $puntos < $n['min']);

        return [
            'nombre'    => $nivelActual['nombre'],
            'emoji'     => $nivelActual['emoji'],
            'color'     => $nivelActual['color'],
            'progreso'  => $siguiente
                ? round((($puntos - $nivelActual['min']) / ($siguiente['min'] - $nivelActual['min'])) * 100)
                : 100,
            'siguiente' => $siguiente['nombre'] ?? 'Nivel máximo',
            'puntos_siguiente' => $siguiente['min'] ?? $puntos,
        ];
    }
}