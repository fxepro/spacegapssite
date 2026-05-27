<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPosts = Post::published()->featured()->with('categories')->latest('published_at')->take(3)->get();
        $latestPosts = Post::published()->with('categories')->latest('published_at')->take(6)->get();
        $featuredPortfolio = PortfolioItem::published()->featured()->latest()->take(3)->get();
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])->orderByDesc('posts_count')->get()->filter(fn($c) => $c->posts_count > 0)->take(8);

        return view('pages.home', compact('featuredPosts', 'latestPosts', 'featuredPortfolio', 'categories'));
    }
}
