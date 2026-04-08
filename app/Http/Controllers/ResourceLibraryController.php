<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceSave;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResourceLibraryController extends Controller
{
    public function index(Request $request)
    {
        $categoria = $request->get('categoria', 'todos');
        $tipo      = $request->get('tipo', 'todos');
        $busqueda  = $request->get('busqueda', '');
        $userId    = auth()->id();

        $query = Resource::where('is_published', true);

        if ($categoria !== 'todos') {
            $query->where('category', $categoria);
        }

        if ($tipo !== 'todos') {
            $query->where('type', $tipo);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('title', 'like', "%{$busqueda}%")
                  ->orWhere('summary', 'like', "%{$busqueda}%");
            });
        }

        $destacados = Resource::where('is_published', true)
            ->where('is_featured', true)
            ->take(3)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        $recursos = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->through(fn($r) => $this->formatearRecurso($r, $userId));

        $savedIds = ResourceSave::where('user_id', $userId)
            ->pluck('resource_id')
            ->toArray();

        return Inertia::render('Resources/Library', [
            'recursos'   => $recursos,
            'destacados' => $destacados,
            'savedIds'   => $savedIds,
            'categoria'  => $categoria,
            'tipo'       => $tipo,
            'busqueda'   => $busqueda,
        ]);
    }

    public function show(Resource $resource)
    {
        $resource->increment('views');
        $userId = auth()->id();

        $relacionados = Resource::where('is_published', true)
            ->where('category', $resource->category)
            ->where('id', '!=', $resource->id)
            ->take(3)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        return Inertia::render('Resources/Show', [
            'recurso'     => $this->formatearRecurso($resource, $userId, true),
            'relacionados'=> $relacionados,
        ]);
    }

    public function toggleSave(Resource $resource)
    {
        $userId  = auth()->id();
        $existing = ResourceSave::where('user_id', $userId)
            ->where('resource_id', $resource->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        ResourceSave::create([
            'user_id'     => $userId,
            'resource_id' => $resource->id,
        ]);

        return response()->json(['saved' => true]);
    }

    public function guardados()
    {
        $userId   = auth()->id();
        $recursos = Resource::whereHas('saves', fn($q) => $q->where('user_id', $userId))
            ->where('is_published', true)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        return Inertia::render('Resources/Saved', [
            'recursos' => $recursos,
        ]);
    }

    private function formatearRecurso(Resource $r, int $userId, bool $fullContent = false): array
    {
        return [
            'id'           => $r->id,
            'title'        => $r->title,
            'summary'      => $r->summary,
            'content'      => $fullContent ? $r->content : null,
            'category'     => $r->category,
            'type'         => $r->type,
            'image_url'    => $r->image_url,
            'external_url' => $r->external_url,
            'read_time'    => $r->read_time,
            'is_featured'  => $r->is_featured,
            'views'        => $r->views,
            'category_color'=> $r->category_color,
            'is_saved'     => $r->isSavedBy($userId),
            'type_label'   => [
                'article'  => '📄 Artículo',
                'video'    => '🎥 Vídeo',
                'podcast'  => '🎙️ Podcast',
                'exercise' => '🧘 Ejercicio',
            ][$r->type] ?? '📄 Artículo',
        ];
    }
}