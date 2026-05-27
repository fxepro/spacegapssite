@extends('layouts.app')

@section('title', 'Portfolio')
@section('meta_description', 'Selected work, projects, and creative output.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-2xl font-extrabold tracking-tight">Portfolio</h1>
        <p class="mt-1.5 text-sg-muted text-xs">Selected work and projects.</p>
    </div>

    @if($categories->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-10">
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                    class="text-sm px-3 py-1.5 rounded-full border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-indigo-400 dark:hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    @endif

    @if($items->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                <x-portfolio-card :item="$item" />
            @endforeach
        </div>
        <div class="mt-12">{{ $items->links('components.pagination') }}</div>
    @else
        <div class="text-center py-20 text-zinc-400"><p class="text-lg">No portfolio items yet.</p></div>
    @endif
</div>
@endsection
