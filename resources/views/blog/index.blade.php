@extends('layouts.app')

@section('title', 'Blog')
@section('meta_description', 'Essays, ideas, and writing on technology, society, and the world.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="mb-10 text-center">
        <h1 class="font-display text-2xl font-extrabold uppercase tracking-widest">Blog</h1>
        <p class="mt-1.5 text-sg-muted text-xs">Essays, ideas, and writing.</p>
    </div>

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
        <div class="flex flex-wrap justify-center items-center gap-x-8 gap-y-3 mb-12">
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="font-display text-[13px] font-extrabold uppercase tracking-widest text-sg-muted hover:text-sg-ink dark:hover:text-sg-paper transition relative group">
                    {{ $cat->name }}
                    <span class="absolute -bottom-0.5 left-0 w-0 h-[2px] bg-sg-accent group-hover:w-full transition-all duration-200"></span>
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
