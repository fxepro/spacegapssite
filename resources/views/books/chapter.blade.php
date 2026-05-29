@extends('layouts.app')
@section('title', $chapter->title . ' — ' . $book->title)
@section('meta_description', $chapter->subtitle ?? Str::limit(strip_tags($chapter->content ?? ''), 160))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">

    {{-- Top navigation --}}
    <div class="flex items-center justify-between mb-8 text-sm">
        <a href="{{ route('books.show', $book) }}"
            class="flex items-center gap-1.5 text-zinc-400 hover:text-zinc-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $book->title }}
        </a>

        {{-- Chapter picker --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-1.5 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-300">
                Contents
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak
                class="absolute right-0 mt-1 w-72 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-lg z-10 py-2 max-h-80 overflow-y-auto">
                @foreach($allChapters as $i => $ch)
                    <a href="{{ route('books.chapter', [$book, $ch]) }}"
                        class="flex items-baseline gap-2 px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition {{ $ch->id === $chapter->id ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                        <span class="text-xs font-mono text-zinc-300 dark:text-zinc-600 w-4 shrink-0 text-right">{{ $i + 1 }}</span>
                        <span class="text-sm truncate">{{ $ch->title }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Chapter header --}}
    <header class="mb-10">
        <p class="text-xs font-bold uppercase tracking-widest text-indigo-500 dark:text-indigo-400 mb-3">
            Chapter {{ $allChapters->search(fn($c) => $c->id === $chapter->id) + 1 }}
        </p>
        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight text-zinc-900 dark:text-zinc-100">
            {{ $chapter->title }}
        </h1>
        @if($chapter->subtitle)
            <p class="mt-2 text-lg text-zinc-500 dark:text-zinc-400 italic">{{ $chapter->subtitle }}</p>
        @endif
    </header>

    {{-- Chapter content --}}
    @if($chapter->content)
        <div class="prose max-w-none
             prose-headings:font-bold
             prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-a:no-underline hover:prose-a:underline
             prose-blockquote:border-indigo-500 prose-img:rounded-xl">
            {!! $chapter->content !!}
        </div>
    @else
        <p class="text-zinc-400 text-center py-12">Content coming soon.</p>
    @endif

    {{-- Prev / Next --}}
    <nav class="mt-16 pt-8 border-t border-zinc-200 dark:border-zinc-700 grid grid-cols-2 gap-4">
        <div>
            @if($prev)
                <a href="{{ route('books.chapter', [$book, $prev]) }}"
                    class="group flex flex-col gap-0.5 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                    <span class="text-xs text-zinc-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Previous
                    </span>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                        {{ $prev->title }}
                    </span>
                </a>
            @endif
        </div>
        <div class="text-right">
            @if($next)
                <a href="{{ route('books.chapter', [$book, $next]) }}"
                    class="group flex flex-col gap-0.5 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                    <span class="text-xs text-zinc-400 flex items-center gap-1 justify-end">
                        Next
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                        {{ $next->title }}
                    </span>
                </a>
            @else
                <div class="p-4 rounded-xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <span class="text-xs text-zinc-400">End of available chapters</span>
                    <a href="{{ route('books.show', $book) }}" class="block text-sm text-indigo-600 dark:text-indigo-400 hover:underline mt-0.5">Back to book</a>
                </div>
            @endif
        </div>
    </nav>

</div>
@endsection
