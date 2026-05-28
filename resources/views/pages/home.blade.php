@extends('layouts.app')

@section('title', 'SpaceGaps')
@section('meta_description', 'SpaceGaps — writing, research, and ideas on technology, society, and the world.')

@section('content')


<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- LEAD STORY --}}
    @if($featuredPosts->isNotEmpty())
    @php $lead = $featuredPosts->first(); $side = $featuredPosts->skip(1); @endphp

    <div class="border-b border-sg-rule dark:border-sg-rule-dark py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 lg:divide-x lg:divide-sg-rule dark:lg:divide-sg-rule-dark">

            {{-- Main lead --}}
            <article class="lg:col-span-8 lg:pr-8">
                @if($lead->featured_image)
                    <a href="{{ route('blog.show', $lead->slug) }}" class="block mb-5 overflow-hidden aspect-[16/7] bg-sg-rule/20">
                        <img src="{{ $lead->featured_image }}" alt="{{ $lead->title }}" class="w-full h-full object-cover hover:scale-[1.02] transition-transform duration-500">
                    </a>
                @endif
                @if($lead->categories->isNotEmpty())
                    <a href="{{ route('categories.show', $lead->categories->first()->slug) }}"
                       class="text-[11px] font-bold uppercase tracking-widest"
                       style="color: {{ $lead->categories->first()->color }}">{{ $lead->categories->first()->name }}</a>
                @endif
                <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold leading-tight mt-2">
                    <a href="{{ route('blog.show', $lead->slug) }}" class="hover:opacity-70 transition">{{ $lead->title }}</a>
                </h2>
                @if($lead->excerpt)
                    <p class="mt-3 text-sm text-sg-body dark:text-sg-paper/70 font-serif leading-relaxed max-w-2xl">{{ $lead->excerpt }}</p>
                @endif
                <div class="mt-4 flex items-center gap-3 text-xs text-sg-muted">
                    <span class="font-semibold text-sg-body dark:text-sg-paper/70">{{ $lead->author }}</span>
                    <span>·</span>
                    @if($lead->published_at)<time>{{ $lead->published_at->format('M j, Y') }}</time><span>·</span>@endif
                    <span>{{ $lead->reading_time }}</span>
                </div>
            </article>

            {{-- Side features --}}
            @if($side->isNotEmpty())
            <div class="lg:col-span-4 lg:pl-8 mt-8 lg:mt-0 space-y-6 divide-y divide-sg-rule dark:divide-sg-rule-dark">
                @foreach($side as $post)
                <article class="pt-6 first:pt-0">
                    @if($post->categories->isNotEmpty())
                        <a href="{{ route('categories.show', $post->categories->first()->slug) }}"
                           class="text-[10px] font-bold uppercase tracking-widest"
                           style="color: {{ $post->categories->first()->color }}">{{ $post->categories->first()->name }}</a>
                    @endif
                    <h3 class="font-display text-sm font-extrabold leading-snug mt-1">
                        <a href="{{ route('blog.show', $post->slug) }}" class="hover:opacity-70 transition">{{ $post->title }}</a>
                    </h3>
                    @if($post->excerpt)
                        <p class="mt-2 text-sm text-sg-body dark:text-sg-paper/60 font-serif leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                    @endif
                    <p class="mt-2 text-[11px] text-sg-muted">
                        @if($post->published_at){{ $post->published_at->format('M j, Y') }} · @endif{{ $post->reading_time }}
                    </p>
                </article>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- LATEST POSTS --}}
    @if($latestPosts->isNotEmpty())
    <div class="py-8 lg:py-12 border-b border-sg-rule dark:border-sg-rule-dark">
        <div class="flex items-baseline justify-between mb-6">
            <h2 class="font-display text-lg font-extrabold">Latest</h2>
            <a href="{{ route('blog.index') }}" class="text-xs uppercase tracking-widest text-sg-muted hover:text-sg-ink dark:hover:text-sg-paper transition font-semibold">All posts →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 divide-y divide-sg-rule dark:divide-sg-rule-dark md:divide-y-0 md:divide-x">
            @foreach($latestPosts->take(3) as $post)
            <article class="py-6 md:py-0 {{ !$loop->first ? 'md:pl-6 lg:pl-8' : '' }} {{ !$loop->last ? 'md:pr-6 lg:pr-8' : '' }}">
                @if($post->featured_image)
                    <a href="{{ route('blog.show', $post->slug) }}" class="block mb-4 overflow-hidden aspect-video bg-sg-rule/20">
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover hover:scale-[1.02] transition-transform duration-500">
                    </a>
                @endif
                @if($post->categories->isNotEmpty())
                    <a href="{{ route('categories.show', $post->categories->first()->slug) }}"
                       class="text-[10px] font-bold uppercase tracking-widest"
                       style="color: {{ $post->categories->first()->color }}">{{ $post->categories->first()->name }}</a>
                @endif
                <h3 class="font-display text-sm font-extrabold leading-snug mt-1.5">
                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:opacity-70 transition">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                    <p class="mt-2 text-sm text-sg-body dark:text-sg-paper/60 font-serif leading-relaxed line-clamp-2">{{ $post->excerpt }}</p>
                @endif
                <p class="mt-3 text-[11px] text-sg-muted">
                    @if($post->published_at){{ $post->published_at->format('M j, Y') }} · @endif{{ $post->reading_time }}
                </p>
            </article>
            @endforeach
        </div>

        {{-- Second row --}}
        @if($latestPosts->count() > 3)
        <div class="mt-6 pt-6 border-t border-sg-rule dark:border-sg-rule-dark grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 divide-y divide-sg-rule dark:divide-sg-rule-dark sm:divide-y-0 sm:divide-x">
            @foreach($latestPosts->skip(3) as $post)
            <article class="py-5 sm:py-0 flex gap-4 {{ !$loop->first ? 'sm:pl-6' : '' }} {{ !$loop->last ? 'sm:pr-6' : '' }}">
                @if($post->featured_image)
                    <a href="{{ route('blog.show', $post->slug) }}" class="flex-shrink-0 w-20 h-20 overflow-hidden bg-sg-rule/20">
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </a>
                @endif
                <div class="min-w-0">
                    @if($post->categories->isNotEmpty())
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $post->categories->first()->color }}">{{ $post->categories->first()->name }}</span>
                    @endif
                    <h4 class="font-display text-sm font-extrabold leading-snug mt-0.5 line-clamp-3">
                        <a href="{{ route('blog.show', $post->slug) }}" class="hover:opacity-70 transition">{{ $post->title }}</a>
                    </h4>
                    <p class="mt-1 text-[11px] text-sg-muted">{{ $post->published_at?->format('M j') }}</p>
                </div>
            </article>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- TWO-COLUMN: Portfolio + Papers --}}
    <div class="py-8 lg:py-12 grid grid-cols-1 lg:grid-cols-2 gap-0 lg:divide-x lg:divide-sg-rule dark:lg:divide-sg-rule-dark border-b border-sg-rule dark:border-sg-rule-dark">

        {{-- Portfolio --}}
        @if($featuredPortfolio->isNotEmpty())
        <div class="lg:pr-8">
            <div class="flex items-baseline justify-between mb-6">
                <h2 class="font-display text-lg font-extrabold">Selected Work</h2>
                <a href="{{ route('portfolio.index') }}" class="text-xs uppercase tracking-widest text-sg-muted hover:text-sg-ink dark:hover:text-sg-paper transition font-semibold">All →</a>
            </div>
            <div class="space-y-5 divide-y divide-sg-rule dark:divide-sg-rule-dark">
                @foreach($featuredPortfolio as $item)
                <article class="pt-5 first:pt-0 flex gap-4">
                    @if($item->featured_image)
                        <a href="{{ route('portfolio.show', $item->slug) }}" class="flex-shrink-0 w-20 h-20 overflow-hidden bg-sg-rule/20">
                            <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </a>
                    @else
                        <a href="{{ route('portfolio.show', $item->slug) }}" class="flex-shrink-0 w-20 h-20 bg-sg-rule/20 dark:bg-sg-rule-dark/30 flex items-center justify-center">
                            <span class="text-2xl font-display font-black text-sg-muted opacity-30">{{ substr($item->title,0,1) }}</span>
                        </a>
                    @endif
                    <div class="min-w-0">
                        @if($item->categories->isNotEmpty())
                            <span class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $item->categories->first()->color }}">{{ $item->categories->first()->name }}</span>
                        @endif
                        <h3 class="font-display text-sm font-extrabold leading-snug mt-0.5">
                            <a href="{{ route('portfolio.show', $item->slug) }}" class="hover:opacity-70 transition">{{ $item->title }}</a>
                        </h3>
                        @if($item->excerpt)
                            <p class="mt-1 text-xs text-sg-muted line-clamp-2">{{ $item->excerpt }}</p>
                        @endif
                        @if($item->project_date)
                            <p class="mt-1 text-[11px] text-sg-muted">{{ $item->project_date->format('Y') }}</p>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Divider on mobile --}}
        <div class="lg:hidden border-t border-sg-rule dark:border-sg-rule-dark mt-8 pt-8"></div>

        {{-- Papers / Newsletter --}}
        <div class="lg:pl-8">
            <div class="mb-6">
                <h2 class="font-display text-lg font-extrabold">From the Archive</h2>
            </div>
            {{-- Newsletter CTA --}}
            <div class="p-6 border border-sg-rule dark:border-sg-rule-dark bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink mb-6">
                <p class="text-[10px] uppercase tracking-widest font-bold opacity-60 mb-2">Newsletter</p>
                <h3 class="font-display text-sm font-extrabold leading-tight">Get new essays in your inbox.</h3>
                <p class="mt-2 text-sm opacity-70 font-serif">New writing, no noise. Unsubscribe any time.</p>
                @if(session('newsletter_success'))
                    <p class="mt-4 text-sm font-semibold">{{ session('newsletter_success') }}</p>
                @else
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-4 flex gap-2">
                        @csrf
                        <input type="email" name="email" placeholder="your@email.com" required
                            class="flex-1 min-w-0 px-3 py-2 text-sm bg-transparent border border-current placeholder-current/50 focus:outline-none focus:ring-1 focus:ring-current">
                        <button type="submit" class="px-4 py-2 text-xs font-bold uppercase tracking-widest border border-current hover:bg-sg-paper/20 dark:hover:bg-sg-ink/20 transition whitespace-nowrap">
                            Subscribe
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
