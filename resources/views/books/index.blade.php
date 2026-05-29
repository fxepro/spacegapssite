@extends('layouts.app')
@section('title', 'Books')
@section('meta_description', 'Books written by SpaceGaps — ideas across theology, economics, technology and more.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-2xl font-extrabold tracking-tight">Books</h1>
        <p class="mt-1.5 text-sg-muted text-xs">Long-form writing — chapters added as they're written.</p>
    </div>

    @if($books->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <a href="{{ route('books.show', $book) }}"
                    class="group bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden hover:border-zinc-300 dark:hover:border-zinc-700 transition flex flex-col">

                    {{-- Cover --}}
                    @if($book->cover_image)
                        <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                    @else
                        <div class="aspect-[3/4] bg-gradient-to-br from-indigo-50 to-violet-100 dark:from-indigo-950 dark:to-violet-950 flex items-center justify-center">
                            <svg class="w-16 h-16 text-indigo-200 dark:text-indigo-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Meta --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="font-bold text-zinc-900 dark:text-zinc-100 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                            {{ $book->title }}
                        </h2>
                        @if($book->subtitle)
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5 italic">{{ $book->subtitle }}</p>
                        @endif
                        @if($book->excerpt)
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-3 leading-relaxed flex-1">{{ Str::limit($book->excerpt, 120) }}</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500">
                            <span>{{ $book->author }}</span>
                            <span>{{ $book->published_chapters_count }} {{ Str::plural('chapter', $book->published_chapters_count) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-12">{{ $books->links('components.pagination') }}</div>
    @else
        <div class="text-center py-24 text-zinc-400">
            <p class="text-lg">No books published yet.</p>
        </div>
    @endif

</div>
@endsection
