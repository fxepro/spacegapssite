@extends('layouts.admin')
@section('page_title', 'Dashboard')

@section('content')
{{-- Stats grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Posts', 'value' => $stats['posts_total'], 'sub' => $stats['posts_published'] . ' published'],
        ['label' => 'Portfolio', 'value' => $stats['portfolio_total'], 'sub' => 'items'],
        ['label' => 'Papers', 'value' => $stats['papers_total'], 'sub' => 'research papers'],
        ['label' => 'Categories', 'value' => $stats['categories'], 'sub' => $stats['tags'] . ' tags'],
    ] as $stat)
        <div class="bg-white rounded-xl p-5 border border-zinc-200">
            <p class="text-lg font-extrabold text-zinc-900">{{ $stat['value'] }}</p>
            <p class="text-sm font-medium text-zinc-600 mt-0.5">{{ $stat['label'] }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ $stat['sub'] }}</p>
        </div>
    @endforeach
</div>

{{-- Quick actions --}}
<div class="flex flex-wrap gap-3 mb-8">
    <a href="{{ route('admin.posts.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Post</a>
    <a href="{{ route('admin.portfolio.create') }}" class="px-4 py-2 bg-white hover:bg-zinc-50 border border-zinc-300 text-zinc-700 text-sm font-medium rounded-lg transition">+ Portfolio Item</a>
    <a href="{{ route('admin.papers.create') }}" class="px-4 py-2 bg-white hover:bg-zinc-50 border border-zinc-300 text-zinc-700 text-sm font-medium rounded-lg transition">+ New Paper</a>
</div>

{{-- Recent posts --}}
<div class="bg-white rounded-xl border border-zinc-200">
    <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
        <h2 class="font-semibold text-zinc-800">Recent Posts</h2>
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
    </div>
    <div class="divide-y divide-zinc-100">
        @forelse($recentPosts as $post)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-zinc-800 truncate">{{ $post->title }}</p>
                    <p class="text-xs text-zinc-400 mt-0.5">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $post->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                        {{ $post->status }}
                    </span>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-sm text-zinc-400">No posts yet. <a href="{{ route('admin.posts.create') }}" class="text-indigo-600 hover:underline">Create one</a></div>
        @endforelse
    </div>
</div>
@endsection
