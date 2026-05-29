@extends('layouts.app')
@section('title', $book->title)
@section('meta_description', $book->excerpt ?? Str::limit($book->description ?? '', 160))
@section('og_image', $book->cover_image ?? $book->featured_image ?? asset('images/og-default.jpg'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">

    <a href="{{ route('books.index') }}" class="text-sm text-zinc-400 hover:text-zinc-600 transition mb-8 inline-block">← Books</a>

    {{-- Book header --}}
    <div class="flex flex-col sm:flex-row gap-8 mb-12">

        {{-- Cover --}}
        <div class="shrink-0">
            @if($book->cover_image)
                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                    class="w-40 sm:w-48 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 object-cover">
            @else
                <div class="w-40 sm:w-48 aspect-[3/4] rounded-xl bg-gradient-to-br from-indigo-50 to-violet-100 dark:from-indigo-950 dark:to-violet-950 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                    <svg class="w-12 h-12 text-indigo-200 dark:text-indigo-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Book info --}}
        <div class="flex-1">
            @if($book->categories->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($book->categories as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}"
                            class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full transition"
                            style="background-color: {{ $cat->color }}18; color: {{ $cat->color }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <h1 class="text-2xl md:text-3xl font-extrabold leading-tight text-zinc-900 dark:text-zinc-100">
                {{ $book->title }}
            </h1>
            @if($book->subtitle)
                <p class="mt-2 text-lg text-zinc-500 dark:text-zinc-400 italic">{{ $book->subtitle }}</p>
            @endif

            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                By <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $book->author }}</span>
                @if($book->published_at)
                    · <time datetime="{{ $book->published_at->toDateString() }}">{{ $book->published_at->format('Y') }}</time>
                @endif
                · {{ $chapters->count() }} {{ Str::plural('chapter', $chapters->count()) }}
            </p>

            @if($book->description)
                <div class="mt-4 prose prose-sm max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    {!! nl2br(e($book->description)) !!}
                </div>
            @endif

            @if($chapters->isNotEmpty())
                <a href="{{ route('books.chapter', [$book, $chapters->first()]) }}"
                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                    Start Reading
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Table of contents --}}
    @if($chapters->isNotEmpty())
        <section>
            <h2 class="text-base font-extrabold mb-4 text-zinc-800 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-700 pb-3">
                Table of Contents
            </h2>
            <ol class="space-y-1">
                @foreach($chapters as $i => $ch)
                    <li>
                        <a href="{{ route('books.chapter', [$book, $ch]) }}"
                            class="flex items-baseline gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/50 group transition">
                            <span class="text-xs font-mono text-zinc-300 dark:text-zinc-600 w-5 shrink-0 text-right">{{ $i + 1 }}</span>
                            <span class="flex-1">
                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                    {{ $ch->title }}
                                </span>
                                @if($ch->subtitle)
                                    <span class="block text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 italic">{{ $ch->subtitle }}</span>
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-zinc-300 dark:text-zinc-600 group-hover:text-indigo-500 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ol>
        </section>
    @else
        <p class="text-sm text-zinc-400 text-center py-8">No chapters published yet. Check back soon.</p>
    @endif

</div>
@endsection
