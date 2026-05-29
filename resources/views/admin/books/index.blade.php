@extends('layouts.admin')
@section('page_title', 'Books')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Book</a>
</div>

<div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                <th class="px-6 py-3 font-medium">Title</th>
                <th class="px-6 py-3 font-medium hidden md:table-cell">Author</th>
                <th class="px-6 py-3 font-medium text-center">Chapters</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($books as $book)
                <tr class="hover:bg-zinc-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 truncate max-w-xs">{{ $book->title }}</p>
                        @if($book->subtitle)
                            <p class="text-xs text-zinc-400 truncate max-w-xs mt-0.5">{{ $book->subtitle }}</p>
                        @endif
                        @if($book->featured)<span class="text-xs text-amber-500">★ Featured</span>@endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-zinc-500">{{ $book->author }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.books.chapters.index', $book) }}"
                            class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ $book->chapters_count }}
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $book->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $book->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-3">
                        <a href="{{ route('books.show', $book) }}" target="_blank" class="text-zinc-400 hover:text-zinc-600 text-xs">View</a>
                        <a href="{{ route('admin.books.chapters.index', $book) }}" class="text-violet-600 hover:text-violet-800 text-xs font-medium">Chapters</a>
                        <a href="{{ route('admin.books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete this book and all its chapters?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-zinc-400">No books yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($books->hasPages())<div class="px-6 py-4 border-t border-zinc-100">{{ $books->links() }}</div>@endif
</div>
@endsection
