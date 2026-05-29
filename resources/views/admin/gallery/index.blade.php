@extends('layouts.admin')
@section('page_title', 'Gallery')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        {{-- Category filter --}}
        @if($categories->isNotEmpty())
            <form method="GET" action="{{ route('admin.gallery.index') }}">
                <select name="category" onchange="this.form.submit()" class="text-sm border border-zinc-200 rounded-lg px-3 py-1.5 text-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>
    <a href="{{ route('admin.gallery.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Add Image</a>
</div>

@if($images->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($images as $image)
            <div class="group relative bg-zinc-100 rounded-xl overflow-hidden aspect-square border border-zinc-200">
                <img src="{{ $image->image_url }}" alt="{{ $image->title ?? 'Gallery image' }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy">

                {{-- Overlay on hover --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all duration-200 flex flex-col justify-between p-2 opacity-0 group-hover:opacity-100">
                    <div class="flex justify-end gap-1.5">
                        @if($image->featured)
                            <span class="px-2 py-0.5 text-xs bg-amber-400 text-amber-900 rounded-full font-medium">★</span>
                        @endif
                    </div>
                    <div>
                        @if($image->title)
                            <p class="text-white text-xs font-semibold truncate mb-1">{{ $image->title }}</p>
                        @endif
                        @if($image->category)
                            <p class="text-white/60 text-xs truncate mb-2">{{ $image->category }}</p>
                        @endif
                        <div class="flex gap-2">
                            <a href="{{ route('admin.gallery.edit', $image) }}"
                               class="flex-1 py-1 text-center text-xs bg-white/20 hover:bg-white/30 text-white rounded-lg transition">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}" onsubmit="return confirm('Delete image?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-500/70 hover:bg-red-500 text-white rounded-lg transition">
                                    Del
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $images->withQueryString()->links() }}
    </div>
@else
    <div class="bg-white rounded-xl border border-zinc-200 p-16 text-center text-zinc-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No images yet.</p>
        <a href="{{ route('admin.gallery.create') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Add your first image →</a>
    </div>
@endif
@endsection
