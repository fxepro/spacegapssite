@extends('layouts.app')

@section('title', 'Videos — SpaceGaps')
@section('meta_description', 'Video library — talks, demos, and recorded presentations.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">

    <header class="mb-10">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Videos</h1>
        <p class="mt-2 text-sg-muted max-w-xl">Talks, demos, interviews, and recorded presentations.</p>
    </header>

    {{-- Featured videos --}}
    @if($featured->isNotEmpty() && !$category && $videos->currentPage() === 1)
        <section class="mb-14"
                 x-data="{ items: {{ $featured->map->toLightbox()->values()->toJson() }} }">
            <h2 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-5">Featured</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($featured as $i => $vid)
                    <button @click="$store.lb.show(items, {{ $i }})"
                            class="group relative rounded-2xl overflow-hidden bg-zinc-900 aspect-video text-left shadow-lg">
                        @if($vid->thumb)
                            <img src="{{ $vid->thumb }}" alt="{{ $vid->title }}"
                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-60 group-hover:scale-105 transition-all duration-500">
                        @endif
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-white/90 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-zinc-900 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                        <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <p class="text-white font-semibold text-sm truncate">{{ $vid->title }}</p>
                            @if($vid->category)
                                <p class="text-white/50 text-xs mt-0.5">{{ $vid->category }}</p>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('videos.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition
                      {{ !$category ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('videos.index', ['category' => $cat]) }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium transition
                          {{ $category === $cat ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Video grid --}}
    @if($videos->isNotEmpty())
        @php $lbItems = $videos->map(fn($v) => $v->toLightbox())->values()->toJson(); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"
             x-data="{ items: {{ $lbItems }} }">
            @foreach($videos as $i => $video)
                <button @click="$store.lb.show(items, {{ $i }})"
                        class="group text-left rounded-2xl overflow-hidden bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                    {{-- Thumb --}}
                    <div class="relative aspect-video bg-zinc-200 dark:bg-zinc-800 overflow-hidden">
                        @if($video->thumb)
                            <img src="{{ $video->thumb }}" alt="{{ $video->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/25 transition">
                            <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
                                <svg class="w-5 h-5 text-zinc-900 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="p-4">
                        <p class="font-semibold text-sm text-zinc-800 dark:text-zinc-100 line-clamp-2">{{ $video->title }}</p>
                        @if($video->category)
                            <p class="text-xs text-zinc-400 mt-1">{{ $video->category }}</p>
                        @endif
                        @if($video->description)
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5 line-clamp-2">{{ $video->description }}</p>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>

        <div class="mt-10">{{ $videos->withQueryString()->links() }}</div>
    @else
        <div class="py-24 text-center text-zinc-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm">No videos yet.</p>
        </div>
    @endif

</div>
@endsection
