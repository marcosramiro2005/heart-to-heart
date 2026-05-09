<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

// Servicio que gestiona las llamadas a la API de noticias (NewsAPI.org).
// Busca artículos en español sobre salud mental según la categoría elegida.
// Utiliza caché de 1 hora para no gastar peticiones de la API en cada carga de página.
// Si la API falla o no está configurada, devuelve artículos de ejemplo predefinidos.
class NewsService
{
    private $apiKey;  // clave de NewsAPI leída desde config/services.php
    private $baseUrl; // URL base de la API (ej: https://newsapi.org/v2/everything)

    public function __construct()
    {
        $this->apiKey  = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.url');
    }

    // Obtiene artículos filtrados por categoría y texto de búsqueda con paginación.
    // Cada categoría tiene una query predefinida en español para la API.
    public function getArticles(string $categoria = 'general', string $busqueda = '', int $pagina = 1): array
    {
        // Mapa de categorías a queries de búsqueda optimizadas para NewsAPI
        $queries = [
            'salud_mental' => 'salud mental OR bienestar emocional OR psicologia',
            'ansiedad'     => 'ansiedad OR ataques de panico OR estres',
            'depresion'    => 'depresion OR tristeza OR salud mental',
            'mindfulness'  => 'mindfulness OR meditacion OR respiracion consciente',
            'sueno'        => 'insomnio OR calidad del sueno OR descanso',
            'autoestima'   => 'autoestima OR autoconfianza OR desarrollo personal',
            'general'      => 'salud mental OR bienestar psicologico',
        ];

        // Si hay texto de búsqueda libre, usarlo directamente; si no, usar la query de la categoría
        $query = $busqueda ?: ($queries[$categoria] ?? $queries['general']);

        // La clave de caché incluye categoría, búsqueda y página para que cada combinación sea única
        $cacheKey = "news_{$categoria}_{$busqueda}_{$pagina}";

        // Cache::remember devuelve el valor cacheado si existe; si no, ejecuta el callback y lo guarda
        // 3600 segundos = 1 hora de caché para no gastar peticiones de la API
        return Cache::remember($cacheKey, 3600, function () use ($query, $pagina) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'q'        => $query,
                    'language' => 'es',        // solo artículos en español
                    'sortBy'   => 'publishedAt', // los más recientes primero
                    'pageSize' => 12,            // 12 artículos por página
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

                // Si la API respondió con error (4xx, 5xx), usar artículos de fallback
                return $this->articulosFallback();

            } catch (\Exception $e) {
                // Si hay timeout o error de red, usar artículos de fallback para no romper la UI
                return $this->articulosFallback();
            }
        });
    }

    // Limpia y normaliza los artículos de la API eliminando los que no tienen título válido
    // y mapeando los campos de la API al formato interno de la aplicación
    private function limpiarArticulos(array $articles): array
    {
        return collect($articles)
            // Filtrar artículos eliminados (NewsAPI los marca con título '[Removed]')
            ->filter(fn($a) => !empty($a['title']) && $a['title'] !== '[Removed]')
            ->map(fn($a) => [
                'title'        => $a['title'],
                'description'  => $a['description'] ?? 'Sin descripción disponible.',
                'url'          => $a['url'],
                'image_url'    => $a['urlToImage'] ?? null,
                'source_name'  => $a['source']['name'] ?? 'Fuente desconocida',
                'published_at' => $a['publishedAt'] ? date('d/m/Y', strtotime($a['publishedAt'])) : '',
                // Hash MD5 de la URL para usarlo como clave única en el frontend
                'url_hash'     => md5($a['url']),
            ])
            ->values()
            ->toArray();
    }

    // Devuelve 3 artículos de ejemplo cuando la API no está disponible o falla.
    // Esto evita que la página de noticias aparezca vacía ante el usuario.
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