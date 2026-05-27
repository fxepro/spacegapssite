@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt ?? Str::limit(strip_tags($post->content), 160))
@section('og_type', 'article')
@section('og_image', $post->featured_image ?? asset('images/og-default.jpg'))

@section('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Article",
  "headline": "{{ addslashes($post->title) }}",
  "description": "{{ addslashes($post->excerpt ?? '') }}",
  "author": { "@@type": "Person", "name": "{{ $post->author }}" },
  "datePublished": "{{ $post->published_at?->toIso8601String() }}",
  "url": "{{ route('blog.show', $post->slug) }}"
}
</script>
@endsection

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 py-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-zinc-400 mb-8">
        <a href="{{ route('blog.index') }}" class="hover:text-zinc-600 dark:hover:text-zinc-300 transition">Blog</a>
        <span>/</span>
        @foreach($post->categories->take(1) as $cat)
            <a href="{{ route('categories.show', $cat->slug) }}" class="hover:text-zinc-600 dark:hover:text-zinc-300 transition">{{ $cat->name }}</a>
            <span>/</span>
        @endforeach
        <span class="text-zinc-600 dark:text-zinc-300 line-clamp-1">{{ $post->title }}</span>
    </nav>

    {{-- Header --}}
    <header class="mb-8">
        @if($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                        class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full transition"
                        style="background-color: {{ $cat->color }}18; color: {{ $cat->color }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight tracking-tight text-zinc-900 dark:text-white">
            {{ $post->title }}
        </h1>

        @if($post->excerpt)
            <p class="mt-4 text-sm text-sg-muted font-light leading-relaxed">{{ $post->excerpt }}</p>
        @endif

        <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400 border-t border-b border-zinc-200 dark:border-zinc-700 py-4">
            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $post->author }}</span>
            @if($post->published_at)
                <span>·</span>
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('F j, Y') }}</time>
            @endif
            <span>·</span>
            <span>{{ $post->reading_time }}</span>
        </div>
    </header>

    {{-- Featured image --}}
    @if($post->featured_image)
        <div class="mb-10 rounded-2xl overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    @endif

    {{-- Content --}}
    <div class="prose max-w-none
         prose-headings:font-bold
        prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-a:no-underline hover:prose-a:underline
        prose-img:rounded-xl prose-blockquote:border-indigo-500 prose-blockquote:bg-indigo-50 dark:prose-blockquote:bg-indigo-950/30 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r-lg">
        {!! $post->content !!}
    </div>

    {{-- Tags --}}
    @if($post->tags->isNotEmpty())
        <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-2">
            @foreach($post->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}"
                    class="text-sm px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Author bio --}}
    <div class="mt-10 p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 flex gap-4">
        <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
            {{ strtoupper(substr($post->author, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-zinc-900 dark:text-white">{{ $post->author }}</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Writer at SpaceGaps. Exploring ideas at the edge of technology, society, and imagination.</p>
        </div>
    </div>

    {{-- Prev / Next --}}
    <nav class="mt-10 grid grid-cols-2 gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-8">
        @php $prev = $post->previous(); $next = $post->next(); @endphp
        <div>
            @if($prev)
                <p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Previous</p>
                <a href="{{ route('blog.show', $prev->slug) }}" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition line-clamp-2">{{ $prev->title }}</a>
            @endif
        </div>
        <div class="text-right">
            @if($next)
                <p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">Next</p>
                <a href="{{ route('blog.show', $next->slug) }}" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition line-clamp-2">{{ $next->title }}</a>
            @endif
        </div>
    </nav>

    {{-- Related --}}
    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-base font-extrabold mb-6">Related Posts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($related as $rel)
                    <x-post-card :post="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</article>
@endsection
