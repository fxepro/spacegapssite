<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Tag;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $items = PortfolioItem::with('categories')->orderBy('sort_order')->latest()->paginate(20);
        return view('admin.portfolio.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.portfolio.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['gallery'] = $this->parseGallery($request->input('gallery_raw', ''));
        $item = PortfolioItem::create($data);
        $this->syncRelations($item, $request);
        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio item created.');
    }

    public function edit(PortfolioItem $portfolioItem)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $portfolioItem->load('categories', 'tags');
        return view('admin.portfolio.form', compact('portfolioItem', 'categories', 'tags'));
    }

    public function update(Request $request, PortfolioItem $portfolioItem)
    {
        $data = $this->validate($request, $portfolioItem);
        $data['gallery'] = $this->parseGallery($request->input('gallery_raw', ''));
        $portfolioItem->update($data);
        $this->syncRelations($portfolioItem, $request);
        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio item updated.');
    }

    public function destroy(PortfolioItem $portfolioItem)
    {
        $portfolioItem->delete();
        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio item deleted.');
    }

    private function validate(Request $request, ?PortfolioItem $item = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_items,slug' . ($item ? ",{$item->id}" : ''),
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'author' => 'nullable|string|max:100',
            'project_date' => 'nullable|date',
            'client' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer',
            'featured' => 'boolean',
        ]);
    }

    private function parseGallery(string $raw): array
    {
        return array_filter(array_map('trim', explode("\n", $raw)));
    }

    private function syncRelations(PortfolioItem $item, Request $request): void
    {
        $item->categories()->sync($request->input('categories', []));
        $item->tags()->sync($request->input('tags', []));
    }
}
