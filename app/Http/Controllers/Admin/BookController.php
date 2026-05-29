<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::withCount('chapters')->latest()->paginate(20);
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('admin.books.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $book = Book::create($data);
        $this->syncRelations($book, $request);
        return redirect()->route('admin.books.chapters.index', $book)
            ->with('success', 'Book created. Add your first chapter below.');
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        $book->load('categories', 'tags');
        return view('admin.books.form', compact('book', 'categories', 'tags'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $this->validated($request, $book);
        $book->update($data);
        $this->syncRelations($book, $request);
        return redirect()->route('admin.books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted.');
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'title'          => 'required|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:books,slug' . ($book ? ",{$book->id}" : ''),
            'excerpt'        => 'nullable|string|max:2000',
            'description'    => 'nullable|string',
            'cover_image'    => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:500',
            'author'         => 'nullable|string|max:100',
            'status'         => 'required|in:draft,published',
            'featured'       => 'boolean',
            'published_at'   => 'nullable|date',
        ]);
    }

    private function syncRelations(Book $book, Request $request): void
    {
        $book->categories()->sync($request->input('categories', []));
        $book->tags()->sync($request->input('tags', []));
    }
}
