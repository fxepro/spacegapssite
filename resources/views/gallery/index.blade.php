@extends('layouts.app')

@section('title', 'Gallery — SpaceGaps')
@section('meta_description', 'A visual archive of images from research, travel, and projects.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">

    {{-- Page header --}}
    <header class="mb-10">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Gallery</h1>
        <p class="mt-2 text-sg-muted max-w-xl">A visual archive — research, fieldwork, projects, and everything in between.</p>
    </header>

    {{-- Featured strip (only on unfiltered first page) --}}
    @if($featured->isNotEmpty() && !$category && $images->currentPage() === 1)
        <section class="mb-14"
                 x-data="{ images: {{ $featured->map->toLightbox()->values()->toJson() }} }">
            <h2 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-5">Featured</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($featured as $i => $img)
                    <button @click="$store.lb.show(images, {{ $i }})"
                            class="relative overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800 group
                                   {{ $loop->first ? 'col-span-2 row-span-2 aspect-square' : 'aspect-square' }}">
                        <img src="{{ $img->image_url }}"
                             alt="{{ $img->title ?? 'Featured image ' . ($i+1) }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        @if($img->title)
                            <div class="absolute bottom-0 inset-x-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <p class="text-white text-sm font-semibold truncate">{{ $img->title }}</p>
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Category filter tabs --}}
    @if($categories->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('gallery.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition
                      {{ !$category ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('gallery.index', ['category' => $cat]) }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium transition
                          {{ $category === $cat ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Main grid --}}
    @if($images->isNotEmpty())
        @php
            $lightboxImages = $images->map(fn($img) => $img->toLightbox())->values()->toJson();
        @endphp
        <div class="columns-2 sm:columns-3 lg:columns-4 gap-3 space-y-3"
             x-data="{ images: {{ $lightboxImages }} }">
            @foreach($images as $i => $image)
                <div class="break-inside-avoid mb-3">
                    <button @click="$store.lb.show(images, {{ $i }})"
                            class="w-full overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800 group block">
                        <img src="{{ $image->image_url }}"
                             alt="{{ $image->title ?? 'Gallery image ' . ($i+1) }}"
                             class="w-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="lazy">
                        @if($image->title || $image->caption)
                            <div class="px-3 py-2 text-left">
                                @if($image->title)
                                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-200 truncate">{{ $image->title }}</p>
                                @endif
                                @if($image->caption)
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 line-clamp-2">{{ $image->caption }}</p>
                                @endif
                            </div>
                        @endif
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $images->withQueryString()->links() }}
        </div>
    @else
        <div class="py-24 text-center text-zinc-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm">No images in this category yet.</p>
        </div>
    @endif

</div>
@endsection
