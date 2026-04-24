<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceSave;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * ResourceLibraryController
 *
 * Gestiona la biblioteca de recursos de bienestar (artículos, vídeos, podcasts, ejercicios):
 * - Lista recursos publicados con filtros por categoría, tipo y búsqueda
 * - Muestra el detalle completo de un recurso con sus relacionados
 * - Permite guardar/eliminar recursos (toggle)
 * - Muestra los recursos guardados por el usuario
 *
 * Usa el método privado formatearRecurso() para construir el array de datos
 * de forma consistente en todos los métodos públicos.
 */
class ResourceLibraryController extends Controller
{
    /**
     * Muestra la biblioteca con filtros, recursos destacados y paginación.
     * Acepta parámetros GET: categoria, tipo, busqueda.
     */
    public function index(Request $request)
    {
        $categoria = $request->get('categoria', 'todos');
        $tipo      = $request->get('tipo', 'todos');
        $busqueda  = $request->get('busqueda', '');
        $userId    = auth()->id();

        // Solo recursos publicados (is_published = true)
        $query = Resource::where('is_published', true);

        if ($categoria !== 'todos') {
            $query->where('category', $categoria);
        }

        if ($tipo !== 'todos') {
            $query->where('type', $tipo);
        }

        // Búsqueda en título y resumen del recurso
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('title', 'like', "%{$busqueda}%")
                  ->orWhere('summary', 'like', "%{$busqueda}%");
            });
        }

        // Los 3 recursos marcados como destacados para la sección superior
        $destacados = Resource::where('is_published', true)
            ->where('is_featured', true)
            ->take(3)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        // Paginación de 9 en 9; los destacados aparecen primero
        $recursos = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->through(fn($r) => $this->formatearRecurso($r, $userId));

        // IDs de recursos guardados por el usuario (para marcar el icono de guardado)
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

    /**
     * Muestra el detalle completo de un recurso.
     * Incrementa el contador de vistas y carga recursos relacionados de la misma categoría.
     */
    public function show(Resource $resource)
    {
        // Incrementar vistas cada vez que se accede al recurso
        $resource->increment('views');
        $userId = auth()->id();

        // Recursos de la misma categoría, excluyendo el actual
        $relacionados = Resource::where('is_published', true)
            ->where('category', $resource->category)
            ->where('id', '!=', $resource->id)
            ->take(3)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        return Inertia::render('Resources/Show', [
            // fullContent = true para incluir el contenido completo en la vista de detalle
            'recurso'      => $this->formatearRecurso($resource, $userId, true),
            'relacionados' => $relacionados,
        ]);
    }

    /**
     * Alterna el estado guardado de un recurso (guardar / eliminar guardado).
     * Responde con JSON para que el frontend actualice el icono sin recargar.
     */
    public function toggleSave(Resource $resource)
    {
        $userId   = auth()->id();
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

    /**
     * Muestra los recursos que el usuario ha guardado.
     * Filtra solo los publicados por si algún recurso fue despublicado después de guardarse.
     */
    public function guardados()
    {
        $userId   = auth()->id();
        // whereHas filtra los recursos que tienen al menos un save del usuario actual
        $recursos = Resource::whereHas('saves', fn($q) => $q->where('user_id', $userId))
            ->where('is_published', true)
            ->get()
            ->map(fn($r) => $this->formatearRecurso($r, $userId));

        return Inertia::render('Resources/Saved', [
            'recursos' => $recursos,
        ]);
    }

    /**
     * Construye el array de datos de un recurso para enviar a la vista.
     * Centraliza el formateo para que todos los métodos devuelvan la misma estructura.
     *
     * @param  Resource  $r            El modelo del recurso
     * @param  int       $userId       ID del usuario (para saber si está guardado)
     * @param  bool      $fullContent  Si true, incluye el contenido completo; si false, solo el resumen
     * @return array
     */
    private function formatearRecurso(Resource $r, int $userId, bool $fullContent = false): array
    {
        return [
            'id'            => $r->id,
            'title'         => $r->title,
            'summary'       => $r->summary,
            // Solo se envía el contenido completo en la vista de detalle para ahorrar datos
            'content'       => $fullContent ? $r->content : null,
            'category'      => $r->category,
            'type'          => $r->type,
            'image_url'     => $r->image_url,
            'external_url'  => $r->external_url,
            'read_time'     => $r->read_time,
            'is_featured'   => $r->is_featured,
            'views'         => $r->views,
            // Color de fondo según categoría, calculado en el modelo Resource
            'category_color'=> $r->category_color,
            // Comprueba si el usuario tiene guardado este recurso
            'is_saved'      => $r->isSavedBy($userId),
            // Etiqueta legible del tipo de contenido
            'type_label'    => [
                'article'  => '📄 Artículo',
                'video'    => '🎥 Vídeo',
                'podcast'  => '🎙️ Podcast',
                'exercise' => '🧘 Ejercicio',
            ][$r->type] ?? '📄 Artículo',
        ];
    }
}