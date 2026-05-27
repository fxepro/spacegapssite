@extends('layouts.app')

@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-2xl font-extrabold mb-8">Search</h1>

    <form action="{{ route('search') }}" method="GET" class="mb-10">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ $query }}" placeholder="Search posts, papers, portfolio…" autofocus
                class="flex-1 px-4 py-3 rounded-xl border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-lg">
            <button type="submit" class="btn-primary">Search</button>
        </div>
    </form>

    @if($query)
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            {{ $total }} result{{ $total !== 1 ? 's' : '' }} for "<strong class="text-zinc-800 dark:text-zinc-200">{{ $query }}</strong>"
        </p>

        @if($posts->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-sm font-extrabold mb-4 flex items-center gap-2">Blog Posts <span class="text-sm font-normal text-zinc-400">{{ $posts->count() }}</span></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($portfolio->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-sm font-extrabold mb-4 flex items-center gap-2">Portfolio <span class="text-sm font-normal text-zinc-400">{{ $portfolio->count() }}</span></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($portfolio as $item)
                        <x-portfolio-card :item="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($papers->isNotEmpty())
            <section>
                <h2 class="text-sm font-extrabold mb-4 flex items-center gap-2">Papers <span class="text-sm font-normal text-zinc-400">{{ $papers->count() }}</span></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($papers as $paper)
                        <x-paper-card :paper="$paper" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($total === 0)
            <div class="text-center py-16 text-zinc-400">
                <p class="text-lg">No results found for "{{ $query }}".</p>
                <p class="mt-2 text-sm">Try different keywords or browse by category.</p>
            </div>
        @endif
    @endif

</div>
@endsection
