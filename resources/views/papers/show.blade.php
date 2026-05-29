@extends('layouts.app')

@section('title', $paper->title)
@section('meta_description', $paper->excerpt ?? Str::limit($paper->abstract ?? '', 160))
@section('og_type', 'article')
@section('og_image', $paper->featured_image ?? asset('images/og-default.jpg'))

@section('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ScholarlyArticle",
  "headline": "{{ addslashes($paper->title) }}",
  "description": "{{ addslashes($paper->excerpt ?? '') }}",
  "author": { "@@type": "Person", "name": "{{ $paper->author }}" },
  "datePublished": "{{ $paper->published_at?->toIso8601String() }}",
  "url": "{{ route('papers.show', $paper->slug) }}"
}
</script>
@endsection

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 py-12">

    <a href="{{ route('papers.index') }}" class="text-sm text-zinc-400 hover:text-zinc-600 transition mb-6 inline-block">← Papers</a>

    {{-- Header --}}
    <header class="mb-10">
        @if($paper->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($paper->categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                        class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full transition"
                        style="background-color: {{ $cat->color }}18; color: {{ $cat->color }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight">{{ $paper->title }}</h1>

        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700 pb-5">
            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $paper->author }}</span>
            @if($paper->published_at)
                <span>·</span>
                <time datetime="{{ $paper->published_at->toDateString() }}">{{ $paper->published_at->format('F j, Y') }}</time>
            @endif
            <span>·</span>
            <span>{{ $paper->reading_time }}</span>
            @if($paper->pdf_url)
                <span>·</span>
                <a href="{{ $paper->pdf_url }}" target="_blank" rel="noopener"
                    class="flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
            @endif
        </div>
    </header>

    {{-- Abstract --}}
    @if($paper->abstract)
        <div class="mb-10 p-6 bg-zinc-50 dark:bg-zinc-800/50 border-l-4 border-indigo-500 rounded-r-xl">
            <h2 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3">Abstract</h2>
            <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed italic">{{ $paper->abstract }}</p>
        </div>
    @endif

    {{-- Content --}}
    @if($paper->content)
        <div class="prose max-w-none
             prose-headings:font-bold
             prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-a:no-underline hover:prose-a:underline
             prose-blockquote:border-indigo-500 prose-img:rounded-xl">
            {!! $paper->content !!}
        </div>
    @endif

    {{-- Image Gallery --}}
    @if(!empty($paper->gallery))
        <section class="mt-12">
            <h2 class="text-base font-extrabold mb-4 text-zinc-800 dark:text-zinc-100">Figures</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($paper->gallery as $i => $imgUrl)
                    <figure class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                        <img src="{{ $imgUrl }}" alt="Figure {{ $i + 1 }}"
                            class="w-full object-cover" loading="lazy">
                        <figcaption class="px-3 py-1.5 text-xs text-zinc-400 dark:text-zinc-500 text-center">
                            Figure {{ $i + 1 }}
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Citations --}}
    @if(!empty($paper->citations))
        <section class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-extrabold mb-5 text-zinc-800 dark:text-zinc-100">Citations</h2>
            <ol class="space-y-3">
                @foreach($paper->citations as $i => $citation)
                    <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        <span class="shrink-0 font-mono text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 w-6 text-right">
                            [{{ $i + 1 }}]
                        </span>
                        <span>
                            {{ $citation['text'] }}
                            @if(!empty($citation['url']))
                                <a href="{{ $citation['url'] }}" target="_blank" rel="noopener"
                                    class="ml-1.5 text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium break-all">
                                    ↗ Link
                                </a>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- Bibliography (freeform fallback) --}}
    @if($paper->references && empty($paper->citations))
        <section class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-extrabold mb-4 text-zinc-800 dark:text-zinc-100">References</h2>
            <div class="prose prose-sm max-w-none text-zinc-600 dark:text-zinc-400">
                {!! nl2br(e($paper->references)) !!}
            </div>
        </section>
    @endif

    {{-- Tags --}}
    @if($paper->tags->isNotEmpty())
        <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-2">
            @foreach($paper->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}"
                    class="text-sm px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300
                           hover:bg-indigo-100 dark:hover:bg-indigo-900/40 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Related --}}
    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-base font-extrabold mb-6">Related Papers</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($related as $rel)
                    <x-paper-card :paper="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</article>
@endsection
