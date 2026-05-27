<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()->with('categories', 'tags')
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get()
            ->filter(fn($c) => $c->posts_count > 0);

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(Post $post)
    {
        abort_if($post->status !== 'published', 404);

        $post->load('categories', 'tags');

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $post->categories->pluck('id')))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
