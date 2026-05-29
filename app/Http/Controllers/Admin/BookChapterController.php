<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookChapter;
use Illuminate\Http\Request;

class BookChapterController extends Controller
{
    public function index(Book $book)
    {
        $chapters = $book->chapters()->get();
        return view('admin.books.chapters.index', compact('book', 'chapters'));
    }

    public function create(Book $book)
    {
        return view('admin.books.chapters.form', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        $data = $this->validated($request);
        $data['book_id']    = $book->id;
        $data['sort_order'] = $book->chapters()->max('sort_order') + 1;
        BookChapter::create($data);
        return redirect()->route('admin.books.chapters.index', $book)
            ->with('success', 'Chapter added.');
    }

    public function edit(Book $book, BookChapter $chapter)
    {
        return view('admin.books.chapters.form', compact('book', 'chapter'));
    }

    public function update(Request $request, Book $book, BookChapter $chapter)
    {
        $chapter->update($this->validated($request, $chapter));
        return redirect()->route('admin.books.chapters.index', $book)
            ->with('success', 'Chapter updated.');
    }

    public function destroy(Book $book, BookChapter $chapter)
    {
        $chapter->delete();
        // Re-sequence remaining chapters
        $book->chapters()->orderBy('sort_order')->each(function ($c, $i) {
            $c->update(['sort_order' => $i]);
        });
        return redirect()->route('admin.books.chapters.index', $book)
            ->with('success', 'Chapter deleted.');
    }

    public function moveUp(Book $book, BookChapter $chapter)
    {
        $prev = $book->chapters()
            ->where('sort_order', '<', $chapter->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$chapter->sort_order, $prev->sort_order] = [$prev->sort_order, $chapter->sort_order];
            $chapter->save();
            $prev->save();
        }

        return back();
    }

    public function moveDown(Book $book, BookChapter $chapter)
    {
        $next = $book->chapters()
            ->where('sort_order', '>', $chapter->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$chapter->sort_order, $next->sort_order] = [$next->sort_order, $chapter->sort_order];
            $chapter->save();
            $next->save();
        }

        return back();
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function validated(Request $request, ?BookChapter $chapter = null): array
    {
        return $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'slug'       => 'nullable|string|max:255|unique:book_chapters,slug' . ($chapter ? ",{$chapter->id}" : ''),
            'content'    => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status'     => 'required|in:draft,published',
        ]);
    }
}
