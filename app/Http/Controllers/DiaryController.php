<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * DiaryController
 *
 * Gestiona el diario personal del usuario:
 * - Lista todas las entradas con estadísticas y calendario del mes actual
 * - Permite crear nuevas entradas con texto, estado de ánimo y etiquetas
 * - Permite eliminar entradas propias
 * - Calcula la racha de días consecutivos con entradas
 */
class DiaryController extends Controller
{
    /**
     * Muestra el diario completo del usuario autenticado.
     * Incluye todas las entradas, estadísticas semanales y el calendario del mes.
     */
    public function index()
    {
        $userId = auth()->id();

        // Obtener todas las entradas del usuario, de más reciente a más antigua
        $entradas = DiaryEntry::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($e) => [
                'id'         => $e->id,
                'content'    => $e->content,
                'mood'       => $e->mood,
                'mood_score' => $e->mood_score,
                'tags'       => $e->tags ?? [],
                'fecha'      => $e->created_at->format('d/m/Y'),
                'hora'       => $e->created_at->format('H:i'),
                // Formato largo: "lunes 7 de abril"
                'dia'        => $e->created_at->locale('es')->isoFormat('dddd D [de] MMMM'),
                // Tiempo relativo: "hace 2 días"
                'hace'       => $e->created_at->diffForHumans(),
            ]);

        // Estadísticas del diario para mostrar en tarjetas del panel
        $stats = [
            'total'          => $entradas->count(),
            // Entradas escritas desde el inicio de la semana actual
            'esta_semana'    => DiaryEntry::where('user_id', $userId)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            // Días consecutivos con al menos una entrada
            'racha'          => $this->calcularRacha($userId),
            'mood_promedio'  => round($entradas->avg('mood_score') ?? 0, 1),
            // Estado de ánimo que más veces ha aparecido
            'mood_frecuente' => $entradas->groupBy('mood')
                ->sortByDesc(fn($g) => $g->count())
                ->keys()->first() ?? 'Sin datos',
        ];

        // Calendario del mes: un punto por día con el mejor mood del día
        $calendario = DiaryEntry::where('user_id', $userId)
            ->whereYear('created_at',  now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($e) => $e->created_at->format('Y-m-d'))
            ->map(fn($grupo) => [
                'count'      => $grupo->count(),
                // Se muestra el mood de la entrada con mayor puntuación del día
                'mood'       => $grupo->sortByDesc('mood_score')->first()->mood,
                'mood_score' => round($grupo->avg('mood_score'), 1),
            ]);

        return Inertia::render('Diary/Index', [
            'entradas'   => $entradas,
            'stats'      => $stats,
            'calendario' => $calendario,
        ]);
    }

    /**
     * Guarda una nueva entrada en el diario del usuario.
     * El contenido es obligatorio; mood, mood_score y tags son opcionales.
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'content'    => 'required|string|min:10|max:5000',
            'mood'       => 'nullable|string',
            'mood_score' => 'integer|min:1|max:10',
            'tags'       => 'nullable|array',
        ]);

        DiaryEntry::create([
            'user_id'    => auth()->id(),
            'content'    => $request->content,
            'mood'       => $request->mood,
            // Si no se pasa mood_score, se usa 5 como valor neutro
            'mood_score' => $request->mood_score ?? 5,
            'tags'       => $request->tags ?? [],
        ]);

        return back()->with('success', '✍️ Entrada guardada en tu diario');
    }

    /**
     * Elimina una entrada del diario.
     * Solo el propietario puede borrar su propia entrada (comprobación de autorización).
     */
    public function eliminar(DiaryEntry $diaryEntry)
    {
        // Abortar con 403 si el usuario intenta borrar una entrada ajena
        if ($diaryEntry->user_id !== auth()->id()) abort(403);
        $diaryEntry->delete();
        return back()->with('success', 'Entrada eliminada');
    }

    /**
     * Calcula los días consecutivos (racha) en los que el usuario ha escrito en el diario.
     * La racha se rompe si el último registro no es de hoy ni de ayer.
     *
     * @param  int  $userId
     * @return int  Número de días consecutivos
     */
    private function calcularRacha(int $userId): int
    {
        // Obtener fechas únicas de entradas, de más reciente a más antigua
        $dias = DiaryEntry::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('created_at')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($dias->isEmpty()) return 0;

        // Si el último registro no es de hoy ni de ayer, la racha es 0
        if ($dias[0] !== now()->toDateString() &&
            $dias[0] !== now()->subDay()->toDateString()) return 0;

        // Contar días consecutivos comparando diferencias entre fechas
        $racha = 1;
        for ($i = 0; $i < $dias->count() - 1; $i++) {
            $diff = \Carbon\Carbon::parse($dias[$i])
                ->diffInDays(\Carbon\Carbon::parse($dias[$i + 1]));
            if ($diff === 1) $racha++;
            else break; // Se rompe la racha en cuanto hay un salto de más de un día
        }
        return $racha;
    }
}