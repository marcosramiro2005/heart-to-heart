<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.url');
    }

    public function getArticles(string $categoria = 'general', string $busqueda = '', int $pagina = 1): array
    {
        $queries = [
            'salud_mental' => 'salud mental OR bienestar emocional OR psicologia',
            'ansiedad'     => 'ansiedad OR ataques de panico OR estres',
            'depresion'    => 'depresion OR tristeza OR salud mental',
            'mindfulness'  => 'mindfulness OR meditacion OR respiracion consciente',
            'sueno'        => 'insomnio OR calidad del sueno OR descanso',
            'autoestima'   => 'autoestima OR autoconfianza OR desarrollo personal',
            'general'      => 'salud mental OR bienestar psicologico',
        ];

        $query = $busqueda ?: ($queries[$categoria] ?? $queries['general']);

        $cacheKey = "news_{$categoria}_{$busqueda}_{$pagina}";

        // Cachear 1 hora para no gastar peticiones
        return Cache::remember($cacheKey, 3600, function () use ($query, $pagina) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'q'        => $query,
                    'language' => 'es',
                    'sortBy'   => 'publishedAt',
                    'pageSize' => 12,
                    'page'     => $pagina,
                    'apiKey'   => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'articles'     => $this->limpiarArticulos($data['articles'] ?? []),
                        'total'        => $data['totalResults'] ?? 0,
                        'pagina'       => $pagina,
                        'total_paginas'=> ceil(($data['totalResults'] ?? 0) / 12),
                        'error'        => null,
                    ];
                }

                return $this->articulosFallback();

            } catch (\Exception $e) {
                return $this->articulosFallback();
            }
        });
    }

    private function limpiarArticulos(array $articles): array
    {
        return collect($articles)
            ->filter(fn($a) => !empty($a['title']) && $a['title'] !== '[Removed]')
            ->map(fn($a) => [
                'title'        => $a['title'],
                'description'  => $a['description'] ?? 'Sin descripción disponible.',
                'url'          => $a['url'],
                'image_url'    => $a['urlToImage'] ?? null,
                'source_name'  => $a['source']['name'] ?? 'Fuente desconocida',
                'published_at' => $a['publishedAt'] ? date('d/m/Y', strtotime($a['publishedAt'])) : '',
                'url_hash'     => md5($a['url']),
            ])
            ->values()
            ->toArray();
    }

    // Artículos de ejemplo si la API falla
    private function articulosFallback(): array
    {
        return [
            'articles' => [
                [
                    'title'        => '5 técnicas de respiración para reducir la ansiedad',
                    'description'  => 'La respiración consciente es una de las herramientas más poderosas para gestionar el estrés y la ansiedad en el día a día.',
                    'url'          => 'https://www.healthline.com',
                    'image_url'    => null,
                    'source_name'  => 'Heart to Heart',
                    'published_at' => date('d/m/Y'),
                    'url_hash'     => md5('fallback1'),
                ],
                [
                    'title'        => 'Cómo el ejercicio mejora tu salud mental',
                    'description'  => 'Estudios demuestran que 30 minutos de actividad física diaria reducen significativamente los síntomas de depresión y ansiedad.',
                    'url'          => 'https://www.psychologytoday.com',
                    'image_url'    => null,
                    'source_name'  => 'Heart to Heart',
                    'published_at' => date('d/m/Y'),
                    'url_hash'     => md5('fallback2'),
                ],
                [
                    'title'        => 'La importancia del sueño en el bienestar emocional',
                    'description'  => 'Dormir entre 7 y 9 horas diarias es fundamental para mantener el equilibrio emocional y la salud mental.',
                    'url'          => 'https://www.sleepfoundation.org',
                    'image_url'    => null,
                    'source_name'  => 'Heart to Heart',
                    'published_at' => date('d/m/Y'),
                    'url_hash'     => md5('fallback3'),
                ],
            ],
            'total'         => 3,
            'pagina'        => 1,
            'total_paginas' => 1,
            'error'         => 'Mostrando contenido de ejemplo',
        ];
    }
}