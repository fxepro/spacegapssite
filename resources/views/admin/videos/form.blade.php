@extends('layouts.admin')
@section('page_title', isset($video) ? 'Edit Video' : 'Add Video')

@section('content')
<form method="POST" action="{{ isset($video) ? route('admin.videos.update', $video) : route('admin.videos.store') }}" class="max-w-2xl">
    @csrf
    @if(isset($video)) @method('PUT') @endif

    <div class="space-y-5">

        {{-- Video URL + live preview --}}
        <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4"
             x-data="{ url: '{{ old('video_url', $video->video_url ?? '') }}', embedUrl(u) {
                if (!u) return '';
                const yt = u.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                if (yt) return 'https://www.youtube.com/embed/' + yt[1];
                const vm = u.match(/vimeo\.com\/(\d+)/);
                if (vm) return 'https://player.vimeo.com/video/' + vm[1];
                return u;
             }}">

            <div>
                <label class="admin-label">Video URL <span class="text-red-500">*</span></label>
                <input type="url" name="video_url" x-model="url" required
                       class="admin-input font-mono text-sm"
                       placeholder="https://youtube.com/watch?v=… or https://vimeo.com/…">
                @error('video_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Live embed preview --}}
            <div x-show="url" class="rounded-xl overflow-hidden bg-zinc-100 aspect-video">
                <iframe :src="embedUrl(url)" class="w-full h-full" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
            <div>
                <label class="admin-label">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $video->title ?? '') }}" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Description</label>
                <textarea name="description" rows="3" class="admin-input resize-none" placeholder="Brief description shown on the videos page.">{{ old('description', $video->description ?? '') }}</textarea>
            </div>
            <div>
                <label class="admin-label">Thumbnail URL <span class="text-zinc-400 font-normal">— auto-derived from YouTube if blank</span></label>
                <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', $video->thumbnail_url ?? '') }}" class="admin-input font-mono text-sm" placeholder="https://…">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">Category</label>
                    <input type="text" name="category" value="{{ old('category', $video->category ?? '') }}"
                           class="admin-input" list="existing-cats" placeholder="e.g. Talks, Demos…">
                    <datalist id="existing-cats">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="admin-label">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $video->sort_order ?? 0) }}" class="admin-input" min="0">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="featured" value="0">
                <input type="checkbox" name="featured" id="featured" value="1"
                       {{ old('featured', $video->featured ?? false) ? 'checked' : '' }}
                       class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                <label for="featured" class="text-sm text-zinc-700">Featured</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                {{ isset($video) ? 'Update Video' : 'Add Video' }}
            </button>
            <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
        </div>
    </div>
</form>
@endsection
