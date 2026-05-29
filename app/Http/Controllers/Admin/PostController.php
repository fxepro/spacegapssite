<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('categories')->latest()->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $post = Post::create($data);
        $this->syncRelations($post, $request);
        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $post->load('categories', 'tags');
        return view('admin.posts.form', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validate($request, $post);
        $post->update($data);
        $this->syncRelations($post, $request);
        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }

    private function validate(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug' . ($post ? ",{$post->id}" : ''),
            'excerpt' => 'nullable|string|max:2000',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'author' => 'nullable|string|max:100',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);
    }

    private function syncRelations(Post $post, Request $request): void
    {
        $post->categories()->sync($request->input('categories', []));
        $post->tags()->sync($request->input('tags', []));
    }
}
