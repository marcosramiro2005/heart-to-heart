<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $userId    = auth()->id();
        $categoria = $request->get('categoria', 'todos');
        $busqueda  = $request->get('busqueda', '');
        $orden     = $request->get('orden', 'reciente');

        $query = ForumPost::with(['user', 'comments', 'likes'])
            ->withCount(['comments', 'likes']);

        if ($categoria !== 'todos') {
            $query->where(function($q) use ($categoria) {
                $q->where('categoria', $categoria)
                  ->orWhere('category', $categoria);
            });
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('title',    'like', "%{$busqueda}%")
                  ->orWhere('content','like', "%{$busqueda}%");
            });
        }

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

        $posts = $query->paginate(10)->through(function ($post) use ($userId) {
            $cat = $post->categoria ?? $post->category ?? 'general';
            return [
                'id'            => $post->id,
                'title'         => $post->title,
                'content'       => substr($post->content, 0, 200) . (strlen($post->content) > 200 ? '...' : ''),
                'categoria'     => $cat,
                'is_featured'   => $post->is_featured ?? false,
                'is_anonymous'  => $post->is_anonymous,
                'views'         => $post->views ?? 0,
                'likes_count'   => $post->likes_count,
                'comments_count'=> $post->comments_count,
                'liked'         => $post->likes->contains('user_id', $userId),
                'is_mine'       => $post->user_id === $userId,
                'autor'         => $post->is_anonymous ? 'Anónimo/a' : ($post->user->name ?? 'Usuario'),
                'avatar'        => $post->is_anonymous ? '🎭' : ($post->user->avatar ?? '👤'),
                'fecha'         => $post->created_at->diffForHumans(),
                'fecha_exacta'  => $post->created_at->format('d/m/Y H:i'),
            ];
        });

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

        $stats = [
            'total_posts'    => ForumPost::count(),
            'posts_hoy'      => ForumPost::whereDate('created_at', today())->count(),
            'total_usuarios' => ForumPost::distinct('user_id')->count('user_id'),
        ];

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

    public function show(ForumPost $forumPost)
    {
        $forumPost->load(['user', 'comments.user']);
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
            'liked'         => ForumLike::where('user_id', auth()->id())
                                ->where('forum_post_id', $forumPost->id)
                                ->exists(),
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

        $forumPost->increment('comments_count');

        return back()->with('success', 'Comentario añadido 💙');
    }

    public function like(ForumPost $forumPost)
    {
        $userId = auth()->id();
        $like   = ForumLike::where('user_id', $userId)
            ->where('forum_post_id', $forumPost->id)
            ->first();

        if ($like) {
            $like->delete();
            $forumPost->decrement('likes_count');
            $liked = false;
        } else {
            ForumLike::create([
                'user_id'       => $userId,
                'forum_post_id' => $forumPost->id,
            ]);
            $forumPost->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $forumPost->fresh()->likes_count,
        ]);
    }

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