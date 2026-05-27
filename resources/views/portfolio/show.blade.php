@extends('layouts.app')

@section('title', $portfolioItem->title)
@section('meta_description', $portfolioItem->excerpt ?? Str::limit(strip_tags($portfolioItem->content), 160))
@section('og_image', $portfolioItem->featured_image ?? asset('images/og-default.jpg'))

@section('content')

{{-- Hero image --}}
@if($portfolioItem->featured_image)
<div class="w-full max-h-[60vh] overflow-hidden bg-zinc-900">
    <img src="{{ $portfolioItem->featured_image }}" alt="{{ $portfolioItem->title }}" class="w-full h-full object-cover opacity-90">
</div>
@endif

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">

    {{-- Header --}}
    <header class="mb-10">
        <a href="{{ route('portfolio.index') }}" class="text-sm text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition mb-4 inline-block">← Portfolio</a>

        @if($portfolioItem->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($portfolioItem->categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                        class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full transition"
                        style="background-color: {{ $cat->color }}18; color: {{ $cat->color }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight">{{ $portfolioItem->title }}</h1>

        @if($portfolioItem->excerpt)
            <p class="mt-4 text-sm text-sg-muted font-light">{{ $portfolioItem->excerpt }}</p>
        @endif

        {{-- Meta grid --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-b border-zinc-200 dark:border-zinc-700 py-6">
            @if($portfolioItem->client)
                <div><p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Client</p><p class="text-sm font-medium">{{ $portfolioItem->client }}</p></div>
            @endif
            @if($portfolioItem->role)
                <div><p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Role</p><p class="text-sm font-medium">{{ $portfolioItem->role }}</p></div>
            @endif
            @if($portfolioItem->project_date)
                <div><p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Year</p><p class="text-sm font-medium">{{ $portfolioItem->project_date->format('Y') }}</p></div>
            @endif
            @if($portfolioItem->external_url)
                <div>
                    <p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Link</p>
                    <a href="{{ $portfolioItem->external_url }}" target="_blank" rel="noopener" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View project →</a>
                </div>
            @endif
        </div>
    </header>

    {{-- Content --}}
    @if($portfolioItem->content)
        <div class="prose max-w-none
             prose-a:text-indigo-600 dark:prose-a:text-indigo-400
            prose-img:rounded-xl">
            {!! $portfolioItem->content !!}
        </div>
    @endif

    {{-- Image Gallery with Lightbox --}}
    @if($portfolioItem->gallery && count($portfolioItem->gallery) > 0)
        <section class="mt-12" x-data="lightbox({{ json_encode($portfolioItem->gallery) }})">
            <h2 class="text-base font-extrabold mb-6">Gallery</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($portfolioItem->gallery as $i => $image)
                    <button @click="open({{ $i }})" class="overflow-hidden rounded-xl aspect-square bg-zinc-100 dark:bg-zinc-800 group">
                        <img src="{{ $image }}" alt="Gallery {{ $i + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </button>
                @endforeach
            </div>

            {{-- Lightbox overlay --}}
            <div x-show="isOpen" x-cloak @keydown.escape.window="close()"
                class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4"
                @click.self="close()">
                <button @click="close()" class="absolute top-4 right-4 text-white/70 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <button @click="prev()" class="absolute left-4 text-white/70 hover:text-white p-2">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <img :src="images[current]" class="max-h-[90vh] max-w-full rounded-lg object-contain">
                <button @click="next()" class="absolute right-4 text-white/70 hover:text-white p-2">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute bottom-4 text-white/50 text-sm" x-text="`${current + 1} / ${images.length}`"></div>
            </div>
        </section>
    @endif

    {{-- Tags --}}
    @if($portfolioItem->tags->isNotEmpty())
        <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-2">
            @foreach($portfolioItem->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}"
                    class="text-sm px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Related --}}
    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-base font-extrabold mb-6">More Work</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($related as $rel)
                    <x-portfolio-card :item="$rel" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
function lightbox(images) {
    return {
        images, isOpen: false, current: 0,
        open(i) { this.current = i; this.isOpen = true; document.body.style.overflow = 'hidden'; },
        close() { this.isOpen = false; document.body.style.overflow = ''; },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
        next() { this.current = (this.current + 1) % this.images.length; },
    };
}
</script>
@endpush
