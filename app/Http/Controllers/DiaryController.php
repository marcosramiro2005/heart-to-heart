<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiaryController extends Controller
{
    public function index()
    {
        $userId  = auth()->id();

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
                'dia'        => $e->created_at->locale('es')->isoFormat('dddd D [de] MMMM'),
                'hace'       => $e->created_at->diffForHumans(),
            ]);

        $stats = [
            'total'          => $entradas->count(),
            'esta_semana'    => DiaryEntry::where('user_id', $userId)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'racha'          => $this->calcularRacha($userId),
            'mood_promedio'  => round($entradas->avg('mood_score') ?? 0, 1),
            'mood_frecuente' => $entradas->groupBy('mood')
                ->sortByDesc(fn($g) => $g->count())
                ->keys()->first() ?? 'Sin datos',
        ];

        $calendario = DiaryEntry::where('user_id', $userId)
            ->whereYear('created_at',  now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($e) => $e->created_at->format('Y-m-d'))
            ->map(fn($grupo) => [
                'count'      => $grupo->count(),
                'mood'       => $grupo->sortByDesc('mood_score')->first()->mood,
                'mood_score' => round($grupo->avg('mood_score'), 1),
            ]);

        return Inertia::render('Diary/Index', [
            'entradas'   => $entradas,
            'stats'      => $stats,
            'calendario' => $calendario,
        ]);
    }

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
            'mood_score' => $request->mood_score ?? 5,
            'tags'       => $request->tags ?? [],
        ]);

        return back()->with('success', '✍️ Entrada guardada en tu diario');
    }

    public function eliminar(DiaryEntry $diaryEntry)
    {
        if ($diaryEntry->user_id !== auth()->id()) abort(403);
        $diaryEntry->delete();
        return back()->with('success', 'Entrada eliminada');
    }

    private function calcularRacha(int $userId): int
    {
        $dias = DiaryEntry::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('created_at')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($dias->isEmpty()) return 0;
        if ($dias[0] !== now()->toDateString() &&
            $dias[0] !== now()->subDay()->toDateString()) return 0;

        $racha = 1;
        for ($i = 0; $i < $dias->count() - 1; $i++) {
            $diff = \Carbon\Carbon::parse($dias[$i])
                ->diffInDays(\Carbon\Carbon::parse($dias[$i + 1]));
            if ($diff === 1) $racha++;
            else break;
        }
        return $racha;
    }
}