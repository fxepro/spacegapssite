<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\Category;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::published()->with('categories', 'tags')
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::withCount(['papers' => fn($q) => $q->published()])
            ->orderByDesc('papers_count')
            ->get()
            ->filter(fn($c) => $c->papers_count > 0);

        return view('papers.index', compact('papers', 'categories'));
    }

    public function show(Paper $paper)
    {
        abort_if($paper->status !== 'published', 404);

        $paper->load('categories', 'tags');

        $related = Paper::published()
            ->where('id', '!=', $paper->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('papers.show', compact('paper', 'related'));
    }
}
