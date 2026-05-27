@extends('layouts.app')

@section('title', '#' . $tag->name)
@section('meta_description', "All content tagged #{$tag->name}.")

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-2xl font-extrabold">#{{ $tag->name }}</h1>
        <p class="mt-1.5 text-sg-muted text-xs">All content tagged with this term.</p>
    </div>

    @if($posts->isNotEmpty())
        <section class="mb-14">
            <h2 class="text-sm font-extrabold mb-5 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-sm">Posts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    @if($portfolio->isNotEmpty())
        <section class="mb-14">
            <h2 class="text-sm font-extrabold mb-5 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-sm">Portfolio</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($portfolio as $item)
                    <x-portfolio-card :item="$item" />
                @endforeach
            </div>
        </section>
    @endif

    @if($papers->isNotEmpty())
        <section>
            <h2 class="text-sm font-extrabold mb-5 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-sm">Papers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($papers as $paper)
                    <x-paper-card :paper="$paper" />
                @endforeach
            </div>
        </section>
    @endif

    @if($posts->isEmpty() && $portfolio->isEmpty() && $papers->isEmpty())
        <div class="text-center py-20 text-zinc-400"><p>No content with this tag yet.</p></div>
    @endif

</div>
@endsection
