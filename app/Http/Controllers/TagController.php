<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $posts = $tag->posts()->published()->with('categories')->latest('published_at')->paginate(10);
        $portfolio = $tag->portfolioItems()->published()->latest()->get();
        $papers = $tag->papers()->published()->latest('published_at')->get();

        return view('tags.show', compact('tag', 'posts', 'portfolio', 'papers'));
    }
}
