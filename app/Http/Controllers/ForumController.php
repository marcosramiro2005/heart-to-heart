<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

/**
 * ForumController
 *
 * Controla toda la funcionalidad del foro de la comunidad:
 * - Lista posts con filtros por categoría, búsqueda y orden
 * - Muestra posts destacados y trending de los últimos 7 días
 * - Permite crear posts y comentarios (con opción de anonimato)
 * - Gestiona el sistema de "me gusta" (toggle: dar/quitar like)
 * - Permite eliminar los propios posts
 */
class ForumController extends Controller
{
    /**
     * Lista los posts del foro con filtros, paginación y secciones laterales.
     * Acepta parámetros GET: categoria, busqueda, orden.
     */
    public function index(Request $request)
    {
        $userId    = auth()->id();
        $categoria = $request->get('categoria', 'todos');
        $busqueda  = $request->get('busqueda', '');
        $orden     = $request->get('orden', 'reciente');

        // Consulta base con las relaciones necesarias
        $query = ForumPost::with(['user', 'comments', 'likes'])
            ->withCount(['comments', 'likes']);

        // Filtro por categoría (soporta tanto 'categoria' como 'category' por retrocompatibilidad)
        if ($categoria !== 'todos') {
            $query->where(function($q) use ($categoria) {
                $q->where('categoria', $categoria)
                  ->orWhere('category', $categoria);
            });
        }

        // Búsqueda en título y contenido del post
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('title',    'like', "%{$busqueda}%")
                  ->orWhere('content','like', "%{$busqueda}%");
            });
        }

        // Ordenación: por popularidad (likes), comentarios o fecha
        switch ($orden) {
            case 'popular':
                $query->orderByDesc('likes_count');
                break;
            case 'comentado':
                $query->orderByDesc('comments_count');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        // Paginar y transformar cada post al formato esperado por la vista
        $posts = $query->paginate(10)->through(function ($post) use ($userId) {
            $cat = $post->categoria ?? $post->category ?? 'general';
            return [
                'id'            => $post->id,
                'title'         => $post->title,
                // Se trunca el contenido a 200 caracteres para el listado
                'content'       => substr($post->content, 0, 200) . (strlen($post->content) > 200 ? '...' : ''),
                'categoria'     => $cat,
                'is_featured'   => $post->is_featured ?? false,
                'is_anonymous'  => $post->is_anonymous,
                'views'         => $post->views ?? 0,
                'likes_count'   => $post->likes_count,
                'comments_count'=> $post->comments_count,
                // Indica si el usuario actual ya dio like a este post
                'liked'         => $post->likes->contains('user_id', $userId),
                // Indica si el post pertenece al usuario actual (para mostrar botón de eliminar)
                'is_mine'       => $post->user_id === $userId,
                // Si es anónimo, se oculta nombre y avatar del autor
                'autor'         => $post->is_anonymous ? 'Anónimo/a' : ($post->user->name ?? 'Usuario'),
                'avatar'        => $post->is_anonymous ? '🎭' : ($post->user->avatar ?? '👤'),
                'fecha'         => $post->created_at->diffForHumans(),
                'fecha_exacta'  => $post->created_at->format('d/m/Y H:i'),
            ];
        });

        // Posts marcados como destacados por el administrador (máximo 3)
        $destacados = ForumPost::with(['user', 'comments', 'likes'])
            ->withCount(['comments', 'likes'])
            ->where('is_featured', true)
            ->orderByDesc('created_at')
            ->take(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id'            => $post->id,
                    'title'         => $post->title,
                    'categoria'     => $post->categoria ?? $post->category ?? 'general',
                    'likes_count'   => $post->likes_count,
                    'comments_count'=> $post->comments_count,
                    'autor'         => $post->is_anonymous ? 'Anónimo/a' : ($post->user->name ?? 'Usuario'),
                    'avatar'        => $post->is_anonymous ? '🎭' : ($post->user->avatar ?? '👤'),
                    'fecha'         => $post->created_at->diffForHumans(),
                ];
            });

        // Posts en tendencia: los más activos (likes + comentarios) de los últimos 7 días
        $trending = ForumPost::withCount(['comments', 'likes'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByRaw('likes_count + comments_count DESC')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'title' => $p->title,
                'score' => $p->likes_count + $p->comments_count,
            ]);

        // Estadísticas globales del foro — cacheadas 30 min para no recalcularlas en cada carga
        $stats = Cache::remember('forum_stats', 1800, fn() => [
            'total_posts'    => ForumPost::count(),
            'posts_hoy'      => ForumPost::whereDate('created_at', today())->count(),
            'total_usuarios' => ForumPost::distinct('user_id')->count('user_id'),
        ]);

        return Inertia::render('Forum/Index', [
            'posts'      => $posts,
            'destacados' => $destacados,
            'trending'   => $trending,
            'stats'      => $stats,
            'categoria'  => $categoria,
            'busqueda'   => $busqueda,
            'orden'      => $orden,
        ]);
    }

    /**
     * Muestra el detalle de un post específico con todos sus comentarios.
     * Incrementa el contador de visitas del post en cada carga.
     */
    public function show(ForumPost $forumPost)
    {
        // Carga las relaciones: autor del post y autores de comentarios
        $forumPost->load(['user', 'comments.user']);
        // Incrementa el contador de vistas (equivalente a +1 en la columna views)
        $forumPost->increment('views');

        $post = [
            'id'            => $forumPost->id,
            'title'         => $forumPost->title,
            'content'       => $forumPost->content,
            'categoria'     => $forumPost->categoria ?? $forumPost->category ?? 'general',
            'is_anonymous'  => $forumPost->is_anonymous,
            'likes_count'   => $forumPost->likes_count,
            'comments_count'=> $forumPost->comments_count,
            'autor'         => $forumPost->is_anonymous ? 'Anónimo/a' : ($forumPost->user->name ?? 'Usuario'),
            'avatar'        => $forumPost->is_anonymous ? '🎭' : ($forumPost->user->avatar ?? '👤'),
            'created_at'    => $forumPost->created_at->diffForHumans(),
            'is_mine'       => $forumPost->user_id === auth()->id(),
            // Comprueba si el usuario actual ya ha dado like a este post
            'liked'         => ForumLike::where('user_id', auth()->id())
                                ->where('forum_post_id', $forumPost->id)
                                ->exists(),
            // Transforma cada comentario ocultando el autor si es anónimo
            'comments'      => $forumPost->comments->map(fn($c) => [
                'id'         => $c->id,
                'content'    => $c->content,
                'autor'      => $c->is_anonymous ?? false ? 'Anónimo/a' : ($c->user->name ?? 'Usuario'),
                'avatar'     => $c->is_anonymous ?? false ? '🎭' : ($c->user->avatar ?? '👤'),
                'created_at' => $c->created_at->diffForHumans(),
                'is_mine'    => $c->user_id === auth()->id(),
            ]),
        ];

        return Inertia::render('Forum/Show', ['post' => $post]);
    }

    /**
     * Crea un nuevo post en el foro.
     * La categoría se guarda en ambas columnas (categoria y category) por compatibilidad.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:200',
            'content'      => 'required|string|max:2000',
            'is_anonymous' => 'boolean',
            'categoria'    => 'nullable|string|in:general,ansiedad,depresion,relaciones,autoestima,duelo,otros',
        ]);

        $cat = $request->categoria ?? 'general';

        ForumPost::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'is_anonymous' => $request->is_anonymous ?? false,
            'categoria'    => $cat,
            'category'     => $cat,
        ]);

        return back()->with('success', '¡Post publicado! 💚');
    }

    /**
     * Añade un comentario a un post.
     * Incrementa manualmente el contador comments_count del post para evitar
     * recalcularlo con withCount() en cada petición.
     */
    public function comment(Request $request, ForumPost $forumPost)
    {
        $request->validate([
            'content'      => 'required|string|min:5|max:500',
            'is_anonymous' => 'boolean',
        ]);

        ForumComment::create([
            'user_id'       => auth()->id(),
            'forum_post_id' => $forumPost->id,
            'content'       => $request->content,
            'is_anonymous'  => $request->is_anonymous ?? false,
        ]);

        // Actualiza el contador desnormalizado del post
        $forumPost->increment('comments_count');

        return back()->with('success', 'Comentario añadido 💙');
    }

    /**
     * Alterna el "me gusta" de un post (dar like / quitar like).
     * Si ya existe un like del usuario, lo elimina; si no existe, lo crea.
     * Responde con JSON para actualizar la UI sin recargar la página.
     */
    public function like(ForumPost $forumPost)
    {
        $userId = auth()->id();
        $like   = ForumLike::where('user_id', $userId)
            ->where('forum_post_id', $forumPost->id)
            ->first();

        if ($like) {
            // Quitar like: eliminar el registro y decrementar el contador
            $like->delete();
            $forumPost->decrement('likes_count');
            $liked = false;
        } else {
            // Dar like: crear el registro e incrementar el contador
            ForumLike::create([
                'user_id'       => $userId,
                'forum_post_id' => $forumPost->id,
            ]);
            $forumPost->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            // Se hace fresh() para obtener el valor actualizado de la base de datos
            'likes_count' => $forumPost->fresh()->likes_count,
        ]);
    }

    /**
     * Elimina un post del foro.
     * Solo el autor puede borrar su propio post (se verifica con abort(403)).
     */
    public function destroy(ForumPost $forumPost)
    {
        if ($forumPost->user_id !== auth()->id()) {
            abort(403);
        }

        $forumPost->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Post eliminado');
    }
}