<?php

namespace App\Http\Controllers;

use App\Models\SavedNews;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * NewsController
 *
 * Gestiona la sección de noticias de salud mental:
 * - Obtiene artículos de la API de noticias (a través de NewsService) con filtros y paginación
 * - Muestra las noticias guardadas por el usuario
 * - Permite guardar/eliminar noticias con un toggle (guardar/desguardar)
 */
class NewsController extends Controller
{
    /**
     * Inyección del servicio de noticias que gestiona las llamadas a la API externa.
     */
    public function __construct(private NewsService $newsService) {}

    /**
     * Muestra el listado de artículos de noticias con filtros.
     * Acepta parámetros GET: categoria, busqueda, pagina.
     * Marca con 'is_saved' los artículos que el usuario ya tiene guardados.
     */
    public function index(Request $request)
    {
        $categoria = $request->get('categoria', 'salud_mental');
        $busqueda  = $request->get('busqueda', '');
        $pagina    = (int) $request->get('pagina', 1);

        // Llamada al servicio que se comunica con la API de NewsAPI
        $data = $this->newsService->getArticles($categoria, $busqueda, $pagina);

        // Obtener las URLs de artículos que el usuario ya guardó para marcarlos en la vista
        $savedUrls = SavedNews::where('user_id', auth()->id())
            ->pluck('article_url')
            ->toArray();

        // Añadir el campo 'is_saved' a cada artículo comparando su URL con las guardadas
        $data['articles'] = array_map(function ($article) use ($savedUrls) {
            $article['is_saved'] = in_array($article['url'], $savedUrls);
            return $article;
        }, $data['articles']);

        return Inertia::render('News/Index', [
            'articulos'    => $data['articles'],
            'total'        => $data['total'],
            'pagina'       => $data['pagina'],
            'totalPaginas' => $data['total_paginas'],
            'categoria'    => $categoria,
            'busqueda'     => $busqueda,
            'error'        => $data['error'], // null si todo fue bien; mensaje si hubo error de API
        ]);
    }

    /**
     * Muestra los artículos que el usuario ha guardado previamente.
     * No llama a la API externa, solo lee de la tabla saved_news.
     */
    public function guardadas()
    {
        $saved = SavedNews::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($s) => [
                'title'        => $s->title,
                'description'  => $s->description,
                'url'          => $s->article_url,
                'image_url'    => $s->image_url,
                'source_name'  => $s->source_name,
                'published_at' => $s->published_at?->format('d/m/Y') ?? '',
                // Hash de la URL para usarlo como clave única en el frontend
                'url_hash'     => md5($s->article_url),
                'is_saved'     => true,
            ]);

        return Inertia::render('News/Guardadas', [
            'articulos' => $saved,
        ]);
    }

    /**
     * Alterna el estado guardado de un artículo (guardar o eliminar).
     * Si el artículo ya está guardado, lo elimina; si no, lo guarda.
     * Responde con JSON para que el frontend actualice el icono sin recargar.
     */
    public function toggleGuardar(Request $request)
    {
        $request->validate([
            'url'         => 'required|url',
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'source_name' => 'nullable|string',
        ]);

        // Buscar si el usuario ya tiene guardado este artículo por su URL
        $existing = SavedNews::where('user_id', auth()->id())
            ->where('article_url', $request->url)
            ->first();

        if ($existing) {
            // Ya está guardado → eliminarlo (desguardar)
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        // No está guardado → guardarlo con todos sus metadatos
        SavedNews::create([
            'user_id'      => auth()->id(),
            'article_url'  => $request->url,
            'title'        => $request->title,
            'description'  => $request->description,
            'image_url'    => $request->image_url,
            'source_name'  => $request->source_name,
            'category'     => $request->categoria ?? 'general',
        ]);

        return response()->json(['saved' => true]);
    }
}