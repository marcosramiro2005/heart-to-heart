<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController extends Controller
{
    // Lista de posts con filtros
    public function index(Request $request)
    {
        $categoria = $request->get('categoria', 'todos');
        $busqueda = $request->get('busqueda', '');

        $query = ForumPost::with('user')
            ->withCount('comments')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($categoria !== 'todos') {
            $query->where('category', $categoria);
        }

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('title', 'like', "%{$busqueda}%")
                  ->orWhere('content', 'like', "%{$busqueda}%");
            });
        }

        $posts = $query->paginate(10)->through(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => substr($post->content, 0, 200),
                'category' => $post->category,
                'is_anonymous' => $post->is_anonymous,
                'is_pinned' => $post->is_pinned,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'author_name' => $post->author_name,
                'created_at' => $post->created_at->diffForHumans(),
                'liked_by_me' => $post->isLikedBy(auth()->id()),
            ];
        });

        return Inertia::render('Forum/Index', [
            'posts' => $posts,
            'categoria' => $categoria,
            'busqueda' => $busqueda,
        ]);
    }

    // Ver post completo con comentarios
    public function show(ForumPost $forumPost)
    {
        $forumPost->load(['user', 'comments.user']);

        $post = [
            'id' => $forumPost->id,
            'title' => $forumPost->title,
            'content' => $forumPost->content,
            'category' => $forumPost->category,
            'is_anonymous' => $forumPost->is_anonymous,
            'likes_count' => $forumPost->likes_count,
            'comments_count' => $forumPost->comments_count,
            'author_name' => $forumPost->author_name,
            'created_at' => $forumPost->created_at->diffForHumans(),
            'liked_by_me' => $forumPost->isLikedBy(auth()->id()),
            'comments' => $forumPost->comments->map(fn($c) => [
                'id' => $c->id,
                'content' => $c->content,
                'author_name' => $c->author_name,
                'created_at' => $c->created_at->diffForHumans(),
                'is_mine' => $c->user_id === auth()->id(),
            ]),
        ];

        return Inertia::render('Forum/Show', ['post' => $post]);
    }

    // Crear nuevo post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string|min:20|max:2000',
            'category' => 'required|in:ansiedad,depresion,relaciones,autoestima,sueno,general',
            'is_anonymous' => 'boolean',
        ]);

        $achievementService = new \App\Services\AchievementService();
        $achievementService->verificarLogros(auth()->id());

        ForumPost::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_anonymous' => $request->is_anonymous ?? false,
        ]);

        return redirect()->route('forum.index')
            ->with('success', '¡Tu experiencia ha sido publicada!');
    }

    // Añadir comentario
    public function comment(Request $request, ForumPost $forumPost)
    {
        $request->validate([
            'content' => 'required|string|min:5|max:500',
            'is_anonymous' => 'boolean',
        ]);

        $achievementService = new \App\Services\AchievementService();
        $achievementService->verificarLogros(auth()->id());

        ForumComment::create([
            'user_id' => auth()->id(),
            'forum_post_id' => $forumPost->id,
            'content' => $request->content,
            'is_anonymous' => $request->is_anonymous ?? false,
        ]);

        $forumPost->increment('comments_count');

        return back()->with('success', 'Comentario añadido');
    }

    // Toggle like
    public function like(ForumPost $forumPost)
    {
        $userId = auth()->id();
        $like = ForumLike::where('user_id', $userId)
            ->where('forum_post_id', $forumPost->id)
            ->first();

        if ($like) {
            $like->delete();
            $forumPost->decrement('likes_count');
            $liked = false;
        } else {
            ForumLike::create([
                'user_id' => $userId,
                'forum_post_id' => $forumPost->id,
            ]);
            $forumPost->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $forumPost->fresh()->likes_count,
        ]);
    }

    // Eliminar post propio
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