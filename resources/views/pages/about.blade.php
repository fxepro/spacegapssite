@extends('layouts.app')

@section('title', 'About')
@section('meta_description', 'About SpaceGaps — the person behind the writing and research.')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="mb-10">
        <h1 class="font-display text-2xl font-extrabold uppercase tracking-wide text-sg-ink dark:text-sg-paper">About</h1>
    </div>

    <div class="prose max-w-none">
        <p>
            SpaceGaps is a personal publication about technology, society, and ideas — written from the belief that the most interesting things happen in the spaces between disciplines.
        </p>

        <h2>Who I Am</h2>
        <p>
            I write about the ideas, systems, and forces that shape how we live and work. The site covers everything from research and long-form essays to shorter blog posts and creative projects.
        </p>

        <h2>What You'll Find Here</h2>
        <ul>
            <li><a href="{{ route('blog.index') }}">Blog</a> — essays, ideas, and commentary</li>
            <li><a href="{{ route('papers.index') }}">Papers</a> — longer research and academic-style writing</li>
            <li><a href="{{ route('portfolio.index') }}">Portfolio</a> — projects and creative work</li>
        </ul>

        <h2>Get in Touch</h2>
        <p>
            You can reach me via the <a href="{{ route('contact') }}">contact page</a>, or subscribe to the newsletter to get new writing delivered to your inbox.
        </p>
    </div>
</div>
@endsection
