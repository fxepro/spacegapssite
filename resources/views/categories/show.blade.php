@extends('layouts.app')

@section('title', $category->name)
@section('meta_description', $category->description ?? "All content in {$category->name}.")

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <a href="{{ route('blog.index') }}" class="text-sm text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition mb-3 inline-block">← All categories</a>
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></span>
            <h1 class="text-2xl font-extrabold">{{ $category->name }}</h1>
        </div>
        @if($category->description)
            <p class="mt-1.5 text-sg-muted text-xs max-w-xl">{{ $category->description }}</p>
        @endif
    </div>

    {{-- Blog posts --}}
    @if($posts->isNotEmpty())
        <section class="mb-16">
            <h2 class="text-base font-extrabold mb-5 text-zinc-700 dark:text-zinc-300">Blog Posts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <x-post-card :post="$post" :featured="true" />
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="mt-8">{{ $posts->links('components.pagination') }}</div>
            @endif
        </section>
    @endif

    {{-- Portfolio --}}
    @if($portfolio->isNotEmpty())
        <section class="mb-16">
            <h2 class="text-base font-extrabold mb-5 text-zinc-700 dark:text-zinc-300">Portfolio</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($portfolio as $item)
                    <x-portfolio-card :item="$item" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Papers --}}
    @if($papers->isNotEmpty())
        <section>
            <h2 class="text-base font-extrabold mb-5 text-zinc-700 dark:text-zinc-300">Papers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($papers as $paper)
                    <x-paper-card :paper="$paper" />
                @endforeach
            </div>
        </section>
    @endif

    @if($posts->isEmpty() && $portfolio->isEmpty() && $papers->isEmpty())
        <div class="text-center py-20 text-zinc-400"><p class="text-lg">No content in this category yet.</p></div>
    @endif

</div>
@endsection
