@extends('layouts.app')

@section('title', ($profile->name ?: 'About') . ' — SpaceGaps')
@section('meta_description', $profile->intro ?? 'About SpaceGaps — the person behind the writing and research.')
@section('og_image', $profile->photo_url ?? asset('images/og-default.jpg'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">

    {{-- ── Hero: photo + name + tagline + social ──────────────────── --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 mb-14">

        {{-- Photo --}}
        @if($profile->photo_url)
            <div class="flex-shrink-0">
                <img src="{{ $profile->photo_url }}" alt="{{ $profile->name }}"
                     class="w-36 h-36 sm:w-44 sm:h-44 rounded-full object-cover border-4 border-zinc-100 dark:border-zinc-800 shadow-lg">
            </div>
        @endif

        <div class="text-center sm:text-left flex-1">
            @if($profile->name)
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-sg-ink dark:text-sg-paper">
                    {{ $profile->name }}
                </h1>
            @else
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-sg-ink dark:text-sg-paper">SpaceGaps</h1>
            @endif

            @if($profile->tagline)
                <p class="mt-2 text-sm font-semibold uppercase tracking-widest text-sg-muted">{{ $profile->tagline }}</p>
            @endif

            @if($profile->location || $profile->email)
                <div class="mt-3 flex flex-wrap justify-center sm:justify-start gap-4 text-sm text-sg-muted">
                    @if($profile->location)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $profile->location }}
                        </span>
                    @endif
                    @if($profile->email)
                        <a href="mailto:{{ $profile->email }}" class="flex items-center gap-1.5 hover:text-sg-ink dark:hover:text-sg-paper transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $profile->email }}
                        </a>
                    @endif
                </div>
            @endif

            {{-- Social links --}}
            @if(!empty($profile->social_links))
                <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-2">
                    @foreach($profile->social_links as $social)
                        @php
                            $icons = [
                                'linkedin'  => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z',
                                'twitter'   => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z',
                                'github'    => 'M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22',
                                'youtube'   => 'M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z',
                                'instagram' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4c0 3.2-2.6 5.8-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2z',
                                'facebook'  => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
                                'substack'  => 'M22 6H2v2h20V6zM2 10v8l10-5 10 5v-8H2zM2 4h20v2H2z',
                                'website'   => 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14',
                            ];
                            $labels = [
                                'linkedin' => 'LinkedIn', 'twitter' => 'Twitter',
                                'github' => 'GitHub', 'youtube' => 'YouTube',
                                'instagram' => 'Instagram', 'facebook' => 'Facebook',
                                'substack' => 'Substack', 'website' => 'Website',
                            ];
                            $icon = $icons[$social['platform']] ?? $icons['website'];
                            $label = $labels[$social['platform']] ?? ucfirst($social['platform']);
                        @endphp
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold
                                  rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300
                                  hover:bg-sg-ink hover:text-sg-paper dark:hover:bg-sg-paper dark:hover:text-sg-ink transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                            </svg>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Short Intro ──────────────────────────────────────────────── --}}
    @if($profile->intro)
        <div class="mb-12 text-lg leading-relaxed text-sg-body dark:text-sg-paper/80 max-w-2xl border-l-4 border-sg-ink dark:border-sg-paper pl-6">
            {{ $profile->intro }}
        </div>
    @endif

    {{-- ── Professional Summary ─────────────────────────────────────── --}}
    @if($profile->summary)
        <div class="prose max-w-none
             prose-headings:font-extrabold
             prose-a:text-indigo-600 dark:prose-a:text-indigo-400
             dark:prose-invert mb-14">
            {!! $profile->summary !!}
        </div>
    @endif

    {{-- ── Video Intro ──────────────────────────────────────────────── --}}
    @if($profile->video_embed_url)
        <div class="mb-14">
            <h2 class="text-base font-extrabold mb-5 uppercase tracking-widest text-zinc-400">Video Intro</h2>
            <div class="rounded-2xl overflow-hidden aspect-video shadow-xl border border-zinc-200 dark:border-zinc-700">
                <iframe src="{{ $profile->video_embed_url }}"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
            </div>
        </div>
    @endif

    {{-- ── Fallback if profile is empty ───────────────────────────── --}}
    @if(!$profile->intro && !$profile->summary && !$profile->name)
        <div class="prose max-w-none dark:prose-invert">
            <p>SpaceGaps is a personal publication about technology, society, and ideas.</p>
            <h2>What You'll Find Here</h2>
            <ul>
                <li><a href="{{ route('blog.index') }}">Blog</a> — essays, ideas, and commentary</li>
                <li><a href="{{ route('papers.index') }}">Papers</a> — longer research and academic-style writing</li>
                <li><a href="{{ route('portfolio.index') }}">Portfolio</a> — projects and creative work</li>
            </ul>
            <p>You can reach me via the <a href="{{ route('contact') }}">contact page</a>.</p>
        </div>
    @endif

</div>
@endsection
