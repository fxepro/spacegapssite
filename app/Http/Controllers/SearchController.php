<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $posts = $papers = $portfolio = collect();

        if (strlen($query) >= 2) {
            $posts = Post::published()
                ->where(fn($q) => $q->where('title', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%"))
                ->with('categories')
                ->latest('published_at')
                ->take(10)
                ->get();

            $portfolio = PortfolioItem::published()
                ->where(fn($q) => $q->where('title', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%"))
                ->latest()
                ->take(6)
                ->get();

            $papers = Paper::published()
                ->where(fn($q) => $q->where('title', 'like', "%{$query}%")->orWhere('abstract', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%"))
                ->latest('published_at')
                ->take(6)
                ->get();
        }

        $total = $posts->count() + $portfolio->count() + $papers->count();

        return view('search.index', compact('query', 'posts', 'portfolio', 'papers', 'total'));
    }
}
