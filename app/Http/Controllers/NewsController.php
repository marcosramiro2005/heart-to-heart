<?php

namespace App\Http\Controllers;

use App\Models\SavedNews;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function __construct(private NewsService $newsService) {}

    public function index(Request $request)
    {
        $categoria = $request->get('categoria', 'salud_mental');
        $busqueda  = $request->get('busqueda', '');
        $pagina    = (int) $request->get('pagina', 1);

        $data = $this->newsService->getArticles($categoria, $busqueda, $pagina);

        $savedUrls = SavedNews::where('user_id', auth()->id())
            ->pluck('article_url')
            ->toArray();

        // Marcar cuáles están guardadas
        $data['articles'] = array_map(function ($article) use ($savedUrls) {
            $article['is_saved'] = in_array($article['url'], $savedUrls);
            return $article;
        }, $data['articles']);

        return Inertia::render('News/Index', [
            'articulos'  => $data['articles'],
            'total'      => $data['total'],
            'pagina'     => $data['pagina'],
            'totalPaginas' => $data['total_paginas'],
            'categoria'  => $categoria,
            'busqueda'   => $busqueda,
            'error'      => $data['error'],
        ]);
    }

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
                'url_hash'     => md5($s->article_url),
                'is_saved'     => true,
            ]);

        return Inertia::render('News/Guardadas', [
            'articulos' => $saved,
        ]);
    }

    public function toggleGuardar(Request $request)
    {
        $request->validate([
            'url'         => 'required|url',
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'source_name' => 'nullable|string',
        ]);

        $existing = SavedNews::where('user_id', auth()->id())
            ->where('article_url', $request->url)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

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