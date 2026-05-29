@extends('layouts.admin')
@section('page_title', $book->title . ' — Chapters')

@section('content')
<div class="max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-zinc-400 mb-6">
        <a href="{{ route('admin.books.index') }}" class="hover:text-zinc-600">Books</a>
        <span>/</span>
        <a href="{{ route('admin.books.edit', $book) }}" class="hover:text-zinc-600">{{ $book->title }}</a>
        <span>/</span>
        <span class="text-zinc-600">Chapters</span>
    </div>

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-semibold text-zinc-800">{{ $book->title }}</h2>
            @if($book->subtitle)<p class="text-sm text-zinc-400">{{ $book->subtitle }}</p>@endif
        </div>
        <a href="{{ route('admin.books.chapters.create', $book) }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
            + New Chapter
        </a>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        @forelse($chapters as $chapter)
            <div class="flex items-center gap-4 px-5 py-4 border-b border-zinc-100 last:border-0 hover:bg-zinc-50">

                {{-- Order controls --}}
                <div class="flex flex-col gap-0.5 shrink-0">
                    <form method="POST" action="{{ route('admin.books.chapters.move-up', [$book, $chapter]) }}">
                        @csrf
                        <button type="submit" title="Move up"
                            class="text-zinc-300 hover:text-zinc-600 transition {{ $loop->first ? 'invisible' : '' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.books.chapters.move-down', [$book, $chapter]) }}">
                        @csrf
                        <button type="submit" title="Move down"
                            class="text-zinc-300 hover:text-zinc-600 transition {{ $loop->last ? 'invisible' : '' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Chapter number --}}
                <span class="text-xs font-mono text-zinc-300 w-6 shrink-0 text-right">{{ $loop->iteration }}</span>

                {{-- Title --}}
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-zinc-800 truncate">{{ $chapter->title }}</p>
                    @if($chapter->subtitle)
                        <p class="text-xs text-zinc-400 truncate mt-0.5">{{ $chapter->subtitle }}</p>
                    @endif
                </div>

                {{-- Status --}}
                <span class="text-xs px-2 py-0.5 rounded-full shrink-0 {{ $chapter->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                    {{ $chapter->status }}
                </span>

                {{-- Actions --}}
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.books.chapters.edit', [$book, $chapter]) }}"
                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                    <form method="POST" action="{{ route('admin.books.chapters.destroy', [$book, $chapter]) }}"
                        class="inline" onsubmit="return confirm('Delete this chapter?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-zinc-400">
                <p class="mb-3">No chapters yet.</p>
                <a href="{{ route('admin.books.chapters.create', $book) }}" class="text-indigo-600 hover:underline text-sm">Add your first chapter →</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.books.edit', $book) }}" class="text-sm text-zinc-500 hover:text-zinc-700">← Book settings</a>
    </div>
</div>
@endsection
