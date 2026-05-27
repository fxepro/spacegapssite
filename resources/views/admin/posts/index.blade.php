@extends('layouts.admin')
@section('page_title', 'Posts')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.posts.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Post</a>
</div>

<div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                <th class="px-6 py-3 font-medium">Title</th>
                <th class="px-6 py-3 font-medium hidden md:table-cell">Categories</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium hidden lg:table-cell">Date</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($posts as $post)
                <tr class="hover:bg-zinc-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 truncate max-w-xs">{{ $post->title }}</p>
                        @if($post->featured)
                            <span class="text-xs text-amber-500">★ Featured</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @foreach($post->categories->take(2) as $cat)
                                <span class="text-xs px-1.5 py-0.5 rounded-full" style="background-color: {{ $cat->color }}20; color: {{ $cat->color }}">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $post->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $post->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell text-zinc-400">
                        {{ $post->published_at?->format('M j, Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-zinc-400 hover:text-zinc-600 mr-3 text-xs">View</a>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-800 mr-3 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-zinc-400">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
