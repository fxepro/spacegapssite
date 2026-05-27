<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Paper;
use App\Models\Tag;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::with('categories')->latest()->paginate(20);
        return view('admin.papers.index', compact('papers'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.papers.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $paper = Paper::create($data);
        $this->syncRelations($paper, $request);
        return redirect()->route('admin.papers.index')->with('success', 'Paper created.');
    }

    public function edit(Paper $paper)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $paper->load('categories', 'tags');
        return view('admin.papers.form', compact('paper', 'categories', 'tags'));
    }

    public function update(Request $request, Paper $paper)
    {
        $data = $this->validate($request, $paper);
        $paper->update($data);
        $this->syncRelations($paper, $request);
        return redirect()->route('admin.papers.index')->with('success', 'Paper updated.');
    }

    public function destroy(Paper $paper)
    {
        $paper->delete();
        return redirect()->route('admin.papers.index')->with('success', 'Paper deleted.');
    }

    private function validate(Request $request, ?Paper $paper = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:papers,slug' . ($paper ? ",{$paper->id}" : ''),
            'excerpt' => 'nullable|string|max:500',
            'abstract' => 'nullable|string',
            'content' => 'nullable|string',
            'references' => 'nullable|string',
            'featured_image' => 'nullable|string|max:500',
            'pdf_url' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'author' => 'nullable|string|max:100',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);
    }

    private function syncRelations(Paper $paper, Request $request): void
    {
        $paper->categories()->sync($request->input('categories', []));
        $paper->tags()->sync($request->input('tags', []));
    }
}
