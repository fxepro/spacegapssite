<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\Category;

class PortfolioController extends Controller
{
    public function index()
    {
        $items = PortfolioItem::published()->with('categories', 'tags')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);

        $categories = Category::withCount(['portfolioItems' => fn($q) => $q->published()])
            ->orderByDesc('portfolio_items_count')
            ->get()
            ->filter(fn($c) => $c->portfolio_items_count > 0);

        return view('portfolio.index', compact('items', 'categories'));
    }

    public function show(PortfolioItem $portfolioItem)
    {
        abort_if($portfolioItem->status !== 'published', 404);

        $portfolioItem->load('categories', 'tags');

        $related = PortfolioItem::published()
            ->where('id', '!=', $portfolioItem->id)
            ->latest()
            ->take(3)
            ->get();

        return view('portfolio.show', compact('portfolioItem', 'related'));
    }
}
