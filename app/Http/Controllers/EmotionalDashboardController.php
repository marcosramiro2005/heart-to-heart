<?php

namespace App\Http\Controllers;

use App\Models\EmotionalRecord;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class EmotionalDashboardController extends Controller
{
    public function index()
    {
        $userId  = auth()->id();
        $user    = auth()->user();

        // Registros últimos 30 días
        $registros = EmotionalRecord::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        // Datos para gráfica de línea
        $graficaLinea = $registros->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
            ->map(fn($grupo) => round($grupo->avg('intensity'), 1))
            ->map(fn($avg, $fecha) => ['fecha' => $fecha, 'intensidad' => $avg]);

        // Datos para gráfica de dona
        $graficaDona = $registros->groupBy('emotion')
            ->map(fn($grupo, $emocion) => [
                'emocion' => $emocion,
                'total'   => $grupo->count(),
            ])
            ->values();

        // Calendario emocional — todos los registros del mes actual
        $mesActual = now()->format('Y-m');
        $calendario = EmotionalRecord::where('user_id', $userId)
            ->whereYear('created_at',  now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
            ->map(fn($grupo) => [
                'emocion'    => $grupo->sortByDesc('intensity')->first()->emotion,
                'intensidad' => round($grupo->avg('intensity'), 1),
                'total'      => $grupo->count(),
            ]);

        // Racha actual
        $racha = $user->rachaActual();

        // Insights automáticos
        $insights = $this->generarInsights($registros, $racha);

        // Estadísticas generales
        $stats = [
            'total_registros'  => EmotionalRecord::where('user_id', $userId)->count(),
            'racha_actual'     => $racha,
            'emocion_frecuente'=> $registros->groupBy('emotion')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'Sin datos',
            'intensidad_media' => round($registros->avg('intensity') ?? 0, 1),
            'mejor_dia'        => $this->mejorDia($registros),
            'dias_registrados' => $registros->groupBy(fn($r) => $r->created_at->format('Y-m-d'))->count(),
        ];

        // Últimos 7 registros
        $ultimosRegistros = EmotionalRecord::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(7)
            ->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'emotion'    => $r->emotion,
                'intensity'  => $r->intensity,
                'note'       => $r->note,
                'fecha'      => $r->created_at->format('d/m/Y'),
                'hora'       => $r->created_at->format('H:i'),
                'emoji'      => $this->emocionEmoji($r->emotion),
            ]);

        return Inertia::render('EmotionalDashboard/Index', [
            'graficaLinea'     => $graficaLinea->values(),
            'graficaDona'      => $graficaDona,
            'calendario'       => $calendario,
            'stats'            => $stats,
            'insights'         => $insights,
            'ultimosRegistros' => $ultimosRegistros,
            'mesActual'        => $mesActual,
        ]);
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'emotion'    => 'required|string',
            'intensity'  => 'required|integer|min:1|max:10',
            'note'       => 'nullable|string|max:500',
            'triggers'   => 'nullable|string|max:200',
            'activities' => 'nullable|string|max:200',
        ]);

        $record = EmotionalRecord::create([
            'user_id'    => auth()->id(),
            'emotion'    => $request->emotion,
            'intensity'  => $request->intensity,
            'note'       => $request->note,
            'triggers'   => $request->triggers,
            'activities' => $request->activities,
        ]);

        // Verificar logros
        app(AchievementService::class)->verificar(auth()->user());

        return back()->with('success', 'Emoción registrada correctamente');
    }

    private function generarInsights($registros, int $racha): array
    {
        $insights = [];

        if ($registros->isEmpty()) {
            return [['tipo' => 'info', 'mensaje' => 'Empieza a registrar tus emociones para ver insights personalizados. 💚']];
        }

        // Insight de racha
        if ($racha >= 7) {
            $insights[] = ['tipo' => 'logro', 'mensaje' => "¡Llevas {$racha} días seguidos registrando tus emociones! 🔥 Eso es compromiso de verdad."];
        } elseif ($racha >= 3) {
            $insights[] = ['tipo' => 'positivo', 'mensaje' => "Llevas {$racha} días seguidos. ¡Sigue así! 💪"];
        }

        // Insight de emoción más frecuente
        $masFrec = $registros->groupBy('emotion')->sortByDesc(fn($g) => $g->count())->keys()->first();
        $vecesFrec = $registros->where('emotion', $masFrec)->count();
        $emojis = ['alegría' => '😊', 'calma' => '😌', 'ansiedad' => '😰', 'tristeza' => '😢', 'enfado' => '😠', 'cansancio' => '😴'];
        $emoji = $emojis[$masFrec] ?? '💙';

        if (in_array($masFrec, ['alegría', 'calma'])) {
            $insights[] = ['tipo' => 'positivo', 'mensaje' => "Tu emoción más frecuente es {$emoji} {$masFrec}. ¡Estás en un buen momento!"];
        } else {
            $insights[] = ['tipo' => 'atencion', 'mensaje' => "Has sentido {$emoji} {$masFrec} {$vecesFrec} veces este mes. Hearty puede ayudarte a gestionarlo."];
        }

        // Insight de tendencia
        $primera = $registros->take(10)->avg('intensity');
        $ultima  = $registros->take(-10)->avg('intensity');

        if ($primera && $ultima) {
            if ($ultima < $primera - 1) {
                $insights[] = ['tipo' => 'positivo', 'mensaje' => 'Tu intensidad emocional ha bajado en las últimas semanas. Las cosas están mejorando. 📈'];
            } elseif ($ultima > $primera + 1) {
                $insights[] = ['tipo' => 'atencion', 'mensaje' => 'Nota que tu intensidad emocional ha subido últimamente. ¿Hay algo que te está afectando más? 💙'];
            } else {
                $insights[] = ['tipo' => 'info', 'mensaje' => 'Tu estado emocional ha sido bastante estable este mes. La estabilidad es bienestar. 🌊'];
            }
        }

        // Insight de días sin registrar
        $diasSinRegistrar = now()->diffInDays(
            $registros->last()?->created_at ?? now()->subDays(30)
        );

        if ($diasSinRegistrar >= 3) {
            $insights[] = ['tipo' => 'recordatorio', 'mensaje' => "Llevas {$diasSinRegistrar} días sin registrar. El autoconocimiento emocional requiere constancia. 📓"];
        }

        return array_slice($insights, 0, 4);
    }

    private function mejorDia($registros): string
    {
        if ($registros->isEmpty()) return 'Sin datos';

        $porDia = $registros->groupBy(fn($r) => $r->created_at->locale('es')->dayName)
            ->map(fn($g) => $g->avg('intensity'));

        return $porDia->sortBy(fn($v) => $v)->keys()->first() ?? 'Sin datos';
    }

    private function emocionEmoji(string $emocion): string
    {
        return [
            'alegría'   => '😊',
            'calma'     => '😌',
            'ansiedad'  => '😰',
            'tristeza'  => '😢',
            'enfado'    => '😠',
            'cansancio' => '😴',
        ][$emocion] ?? '💙';
    }
}