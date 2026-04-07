<?php

namespace App\Http\Controllers;

use App\Models\EmotionalRecord;
use App\Models\BreathingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class EmotionalDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $hoy = Carbon::today();

        // ── Registros últimos 30 días ──
        $registros = EmotionalRecord::where('user_id', $userId)
            ->where('recorded_at', '>=', $hoy->copy()->subDays(29))
            ->orderBy('recorded_at', 'asc')
            ->get();

        // ── Datos para gráfica de línea (evolución intensidad) ──
        $evolucion = $registros->groupBy(fn($r) => Carbon::parse($r->recorded_at)->format('d/m'))
            ->map(fn($grupo) => round($grupo->avg('intensity'), 1))
            ->toArray();

        // ── Distribución de emociones (gráfica de dona) ──
        $distribucion = EmotionalRecord::where('user_id', $userId)
            ->select('emotion', DB::raw('count(*) as total'))
            ->groupBy('emotion')
            ->pluck('total', 'emotion')
            ->toArray();

        // ── Racha actual ──
        $racha = $this->calcularRacha($userId);

        // ── Registro de hoy ──
        $registroHoy = EmotionalRecord::where('user_id', $userId)
            ->whereDate('recorded_at', $hoy)
            ->first();

        // ── Estadísticas generales ──
        $stats = [
            'total_registros'  => EmotionalRecord::where('user_id', $userId)->count(),
            'emocion_frecuente' => $this->emocionMasFrecuente($userId),
            'intensidad_media' => round(EmotionalRecord::where('user_id', $userId)->avg('intensity') ?? 0, 1),
            'sesiones_respira' => BreathingSession::where('user_id', $userId)->count(),
        ];

        // ── Historial reciente ──
        $historial = EmotionalRecord::where('user_id', $userId)
            ->orderByDesc('recorded_at')
            ->take(7)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'emotion'     => $r->emotion,
                'intensity'   => $r->intensity,
                'notes'       => $r->notes,
                'color'       => $r->color ?? $this->colorEmocion($r->emotion),
                'recorded_at' => Carbon::parse($r->recorded_at)->format('d/m/Y'),
            ]);

        return Inertia::render('EmotionalDashboard/Index', [
            'evolucion'     => $evolucion,
            'distribucion'  => $distribucion,
            'racha'         => $racha,
            'registroHoy'   => $registroHoy,
            'stats'         => $stats,
            'historial'     => $historial,
        ]);
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'emotion'   => 'required|string',
            'intensity' => 'required|integer|min:1|max:10',
            'notes'     => 'nullable|string|max:300',
            'activity'  => 'nullable|string',
        ]);

        $hoy = Carbon::today();

        // Un registro por día — actualizar si ya existe
        EmotionalRecord::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'recorded_at' => $hoy,
            ],
            [
                'emotion'   => $request->emotion,
                'intensity' => $request->intensity,
                'notes'     => $request->notes,
                'color'     => $this->colorEmocion($request->emotion),
                'activity'  => $request->activity,
            ]
        );

        return back()->with('success', '¡Emoción registrada! 💚');
    }

    private function calcularRacha(int $userId): array
    {
        $registros = EmotionalRecord::where('user_id', $userId)
            ->select('recorded_at')
            ->orderByDesc('recorded_at')
            ->get()
            ->map(fn($r) => Carbon::parse($r->recorded_at)->startOfDay());

        $racha = 0;
        $hoy = Carbon::today();

        foreach ($registros as $i => $fecha) {
            $esperada = $hoy->copy()->subDays($i);
            if ($fecha->eq($esperada)) {
                $racha++;
            } else {
                break;
            }
        }

        return [
            'dias'     => $racha,
            'mensaje'  => $this->mensajeRacha($racha),
            'emoji'    => $racha >= 7 ? '🔥' : ($racha >= 3 ? '⭐' : '🌱'),
        ];
    }

    private function mensajeRacha(int $dias): string
    {
        if ($dias === 0) return 'Empieza tu racha hoy';
        if ($dias === 1) return '¡Primer día! Sigue así';
        if ($dias < 7)  return "¡{$dias} días seguidos!";
        if ($dias < 30) return "¡{$dias} días! Eres increíble";
        return "¡{$dias} días! Eres un ejemplo";
    }

    private function emocionMasFrecuente(int $userId): string
    {
        $result = EmotionalRecord::where('user_id', $userId)
            ->select('emotion', DB::raw('count(*) as total'))
            ->groupBy('emotion')
            ->orderByDesc('total')
            ->first();

        return $result ? $result->emotion : 'Sin datos';
    }

    private function colorEmocion(string $emotion): string
    {
        return [
            '😊 Alegría'    => '#FFD700',
            '😌 Calma'      => '#4ECDC4',
            '😰 Ansiedad'   => '#FF8C42',
            '😢 Tristeza'   => '#6B9FD4',
            '😠 Enfado'     => '#E63946',
            '😴 Cansancio'  => '#9B8EC4',
        ][$emotion] ?? '#4ECDC4';
    }
}