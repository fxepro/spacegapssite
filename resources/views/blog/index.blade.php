@extends('layouts.app')

@section('title', 'Blog')
@section('meta_description', 'Essays, ideas, and writing on technology, society, and the world.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-2xl font-extrabold tracking-tight">Blog</h1>
        <p class="mt-1.5 text-sg-muted text-xs">Essays, ideas, and writing.</p>
    </div>

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <a href="{{ route('blog.index') }}" class="font-display text-base font-semibold px-4 py-2 rounded-full {{ !request('category') ? 'bg-indigo-600 text-white' : 'border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-indigo-400' }} transition">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                    class="font-display text-base font-semibold px-4 py-2 rounded-full border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-indigo-400 dark:hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    {{ $cat->name }} <span class="text-zinc-400 ml-1 text-sm font-normal">{{ $cat->posts_count }}</span>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Posts grid --}}
    @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <x-post-card :post="$post" :featured="true" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links('components.pagination') }}
        </div>
    @else
        <div class="text-center py-20 text-zinc-400">
            <p class="text-lg">No posts yet.</p>
        </div>
    @endif
</div>
@endsection
