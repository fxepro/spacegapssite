@extends('layouts.admin')
@section('page_title', 'Videos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @if($categories->isNotEmpty())
            <form method="GET" action="{{ route('admin.videos.index') }}">
                <select name="category" onchange="this.form.submit()"
                        class="text-sm border border-zinc-200 rounded-lg px-3 py-1.5 text-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </form>
        @endif
        <span class="text-xs text-zinc-400">{{ $videos->total() }} video{{ $videos->total() !== 1 ? 's' : '' }}</span>
    </div>
    <a href="{{ route('admin.videos.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Add Video</a>
</div>

@if($videos->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($videos as $video)
            <div class="group bg-white rounded-xl border border-zinc-200 overflow-hidden">
                {{-- Thumbnail --}}
                <div class="relative aspect-video bg-zinc-100 overflow-hidden">
                    @if($video->thumb)
                        <img src="{{ $video->thumb }}" alt="{{ $video->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif
                    {{-- Play overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition">
                        <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-5 h-5 text-zinc-800 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                    @if($video->featured)
                        <span class="absolute top-2 left-2 px-1.5 py-0.5 text-xs bg-amber-400 text-amber-900 rounded font-bold">★</span>
                    @endif
                </div>
                {{-- Info --}}
                <div class="p-3">
                    <p class="text-sm font-semibold text-zinc-800 truncate">{{ $video->title }}</p>
                    @if($video->category)
                        <p class="text-xs text-zinc-400 mt-0.5">{{ $video->category }}</p>
                    @endif
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('admin.videos.edit', $video) }}"
                           class="flex-1 py-1 text-center text-xs bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-lg transition font-medium">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition">Del</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $videos->withQueryString()->links() }}</div>
@else
    <div class="bg-white rounded-xl border border-zinc-200 p-16 text-center text-zinc-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm">No videos yet.</p>
        <a href="{{ route('admin.videos.create') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Add your first video →</a>
    </div>
@endif
@endsection
