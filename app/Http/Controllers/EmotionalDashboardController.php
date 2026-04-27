<?php

namespace App\Http\Controllers;

use App\Models\EmotionalRecord;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * EmotionalDashboardController
 *
 * Gestiona todo el panel de emociones del usuario:
 * - Muestra gráficas (línea e intensidad por emoción)
 * - Genera el calendario emocional del mes actual
 * - Calcula estadísticas personales (racha, mejor día, emoción frecuente)
 * - Produce insights automáticos basados en los registros
 * - Permite registrar una nueva emoción
 */
class EmotionalDashboardController extends Controller
{
    /**
     * Muestra el dashboard emocional completo.
     * Recoge los registros de los últimos 30 días y construye
     * todos los datos que necesita la vista (gráficas, calendario, stats, insights).
     */
    public function index()
    {
        $userId  = auth()->id();
        $user    = auth()->user();

        // Registros de los últimos 30 días, ordenados cronológicamente
        $registros = EmotionalRecord::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        // Datos para la gráfica de línea:
        // Agrupa por fecha y calcula la intensidad media de cada día
        $graficaLinea = $registros->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
            ->map(fn($grupo) => round($grupo->avg('intensity'), 1))
            ->map(fn($avg, $fecha) => ['fecha' => $fecha, 'intensidad' => $avg]);

        // Datos para la gráfica de dona:
        // Cuenta cuántas veces se ha registrado cada emoción
        $graficaDona = $registros->groupBy('emotion')
            ->map(fn($grupo, $emocion) => [
                'emocion' => $emocion,
                'total'   => $grupo->count(),
            ])
            ->values();

        // Calendario emocional: un punto por cada día del mes actual.
        // Si hay varios registros en un día, se muestra la emoción de mayor intensidad.
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

        // Racha actual de días consecutivos con registro
        $racha = $user->rachaActual();

        // Mensajes automáticos personalizados según los patrones del usuario
        $insights = $this->generarInsights($registros, $racha);

        // Estadísticas generales del panel
        $stats = [
            'total_registros'  => EmotionalRecord::where('user_id', $userId)->count(),
            'racha_actual'     => $racha,
            // Emoción que más veces ha aparecido en los últimos 30 días
            'emocion_frecuente'=> $registros->groupBy('emotion')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'Sin datos',
            'intensidad_media' => round($registros->avg('intensity') ?? 0, 1),
            // Día de la semana con menor intensidad media (el "mejor" día)
            'mejor_dia'        => $this->mejorDia($registros),
            'dias_registrados' => $registros->groupBy(fn($r) => $r->created_at->format('Y-m-d'))->count(),
        ];

        // Últimas 7 emociones registradas para mostrar en el historial reciente
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

        // Renderiza la vista de Inertia con todos los datos calculados
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

    /**
     * Guarda un nuevo registro emocional en la base de datos.
     * Después de guardarlo, verifica si el usuario ha desbloqueado algún logro.
     */
    public function registrar(Request $request)
    {
        // Validación: emoción e intensidad son obligatorias; el resto son opcionales
        $request->validate([
            'emotion'    => 'required|string',
            'intensity'  => 'required|integer|min:1|max:10',
            'note'       => 'nullable|string|max:500',
            'triggers'   => 'nullable|string|max:200',
            'activities' => 'nullable|string|max:200',
        ]);

        EmotionalRecord::create([
            'user_id'     => auth()->id(),
            'emotion'     => $request->emotion,
            'intensity'   => $request->intensity,
            'note'        => $request->note,
            'triggers'    => $request->triggers,
            'activities'  => $request->activities,
            'recorded_at' => now()->toDateString(),
        ]);

        // Lanzar la comprobación de logros tras registrar una emoción
        app(AchievementService::class)->verificarLogros(auth()->id());

        return back()->with('success', 'Emoción registrada correctamente');
    }

    /**
     * Genera hasta 4 mensajes de insight personalizados basados en el historial.
     * Los tipos posibles son: 'logro', 'positivo', 'atencion', 'info', 'recordatorio'.
     *
     * @param  \Illuminate\Support\Collection  $registros  Registros de los últimos 30 días
     * @param  int  $racha  Días consecutivos de registro
     * @return array  Lista de arrays con 'tipo' y 'mensaje'
     */
    private function generarInsights($registros, int $racha): array
    {
        $insights = [];

        // Si el usuario aún no tiene registros, devuelve un mensaje de bienvenida
        if ($registros->isEmpty()) {
            return [['tipo' => 'info', 'mensaje' => 'Empieza a registrar tus emociones para ver insights personalizados. 💚']];
        }

        // Insight sobre la racha de días consecutivos
        if ($racha >= 7) {
            $insights[] = ['tipo' => 'logro', 'mensaje' => "¡Llevas {$racha} días seguidos registrando tus emociones! 🔥 Eso es compromiso de verdad."];
        } elseif ($racha >= 3) {
            $insights[] = ['tipo' => 'positivo', 'mensaje' => "Llevas {$racha} días seguidos. ¡Sigue así! 💪"];
        }

        // Insight sobre la emoción que más se repite
        $masFrec   = $registros->groupBy('emotion')->sortByDesc(fn($g) => $g->count())->keys()->first();
        $vecesFrec = $registros->where('emotion', $masFrec)->count();
        $emojis    = ['alegría' => '😊', 'calma' => '😌', 'ansiedad' => '😰', 'tristeza' => '😢', 'enfado' => '😠', 'cansancio' => '😴'];
        $emoji     = $emojis[$masFrec] ?? '💙';

        if (in_array($masFrec, ['alegría', 'calma'])) {
            $insights[] = ['tipo' => 'positivo', 'mensaje' => "Tu emoción más frecuente es {$emoji} {$masFrec}. ¡Estás en un buen momento!"];
        } else {
            $insights[] = ['tipo' => 'atencion', 'mensaje' => "Has sentido {$emoji} {$masFrec} {$vecesFrec} veces este mes. Hearty puede ayudarte a gestionarlo."];
        }

        // Insight sobre la tendencia: compara los primeros 10 registros con los últimos 10
        // para detectar si la intensidad emocional ha subido o bajado
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

        // Insight de recordatorio si llevan 3 o más días sin registrar
        $diasSinRegistrar = now()->diffInDays(
            $registros->last()?->created_at ?? now()->subDays(30)
        );

        if ($diasSinRegistrar >= 3) {
            $insights[] = ['tipo' => 'recordatorio', 'mensaje' => "Llevas {$diasSinRegistrar} días sin registrar. El autoconocimiento emocional requiere constancia. 📓"];
        }

        // Devuelve como máximo 4 insights para no saturar la vista
        return array_slice($insights, 0, 4);
    }

    /**
     * Devuelve el día de la semana donde el usuario tiene menor intensidad emocional media.
     * Se usa en las estadísticas como "mejor día".
     *
     * @param  \Illuminate\Support\Collection  $registros
     * @return string  Nombre del día en español, o 'Sin datos' si no hay registros
     */
    private function mejorDia($registros): string
    {
        if ($registros->isEmpty()) return 'Sin datos';

        // Agrupa por nombre del día y calcula la media de intensidad de cada uno
        $porDia = $registros->groupBy(fn($r) => $r->created_at->locale('es')->dayName)
            ->map(fn($g) => $g->avg('intensity'));

        // El "mejor" día es el que tiene la intensidad media más baja
        return $porDia->sortBy(fn($v) => $v)->keys()->first() ?? 'Sin datos';
    }

    /**
     * Devuelve el emoji asociado a una emoción determinada.
     * Si la emoción no está en el mapa, devuelve 💙 por defecto.
     *
     * @param  string  $emocion
     * @return string  Emoji Unicode
     */
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