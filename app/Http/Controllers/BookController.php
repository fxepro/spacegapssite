<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookChapter;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::published()->with('categories')->withCount('publishedChapters')->latest('published_at')->paginate(12);
        return view('books.index', compact('books'));
    }

    public function show(Book $book)
    {
        abort_if($book->status !== 'published', 404);
        $book->load('categories', 'tags');
        $chapters = $book->publishedChapters()->get();
        return view('books.show', compact('book', 'chapters'));
    }

    public function chapter(Book $book, BookChapter $chapter)
    {
        abort_if($book->status !== 'published', 404);
        abort_if($chapter->status !== 'published', 404);
        abort_if($chapter->book_id !== $book->id, 404);

        $allChapters = $book->publishedChapters()->get();
        $currentIndex = $allChapters->search(fn($c) => $c->id === $chapter->id);

        $prev = $currentIndex > 0 ? $allChapters[$currentIndex - 1] : null;
        $next = $currentIndex < $allChapters->count() - 1 ? $allChapters[$currentIndex + 1] : null;

        return view('books.chapter', compact('book', 'chapter', 'allChapters', 'prev', 'next'));
    }
}
