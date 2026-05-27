<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = $category->posts()->published()->with('categories')->latest('published_at')->paginate(10);
        $portfolio = $category->portfolioItems()->published()->latest()->get();
        $papers = $category->papers()->published()->latest('published_at')->get();

        return view('categories.show', compact('category', 'posts', 'portfolio', 'papers'));
    }
}
