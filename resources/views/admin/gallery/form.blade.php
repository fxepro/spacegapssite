@extends('layouts.admin')
@section('page_title', isset($gallery) ? 'Edit Image' : 'Add Image')

@section('content')
<form method="POST" action="{{ isset($gallery) ? route('admin.gallery.update', $gallery) : route('admin.gallery.store') }}" class="max-w-2xl">
    @csrf
    @if(isset($gallery)) @method('PUT') @endif

    <div class="space-y-5">

        {{-- Image URL + preview --}}
        <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4"
             x-data="{ url: '{{ old('image_url', $gallery->image_url ?? '') }}' }">

            <div>
                <label class="admin-label">Image URL <span class="text-red-500">*</span></label>
                <input type="url" name="image_url" x-model="url"
                       value="{{ old('image_url', $gallery->image_url ?? '') }}"
                       required class="admin-input font-mono text-sm"
                       placeholder="https://…">
                @error('image_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Live preview --}}
            <div x-show="url" class="rounded-xl overflow-hidden border border-zinc-200 bg-zinc-50 max-h-72 flex items-center justify-center">
                <img :src="url" alt="Preview" class="max-h-72 max-w-full object-contain" loading="lazy">
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
            <div>
                <label class="admin-label">Title <span class="text-zinc-400 font-normal">— optional, shown in lightbox</span></label>
                <input type="text" name="title" value="{{ old('title', $gallery->title ?? '') }}" class="admin-input" placeholder="Descriptive title">
            </div>
            <div>
                <label class="admin-label">Caption</label>
                <textarea name="caption" rows="2" class="admin-input resize-none" placeholder="Additional context shown below the image in lightbox.">{{ old('caption', $gallery->caption ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">Category <span class="text-zinc-400 font-normal">— free-form label</span></label>
                    <input type="text" name="category" value="{{ old('category', $gallery->category ?? '') }}" class="admin-input"
                           list="existing-cats" placeholder="e.g. Architecture, Travel…">
                    <datalist id="existing-cats">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="admin-label">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $gallery->sort_order ?? 0) }}" class="admin-input" min="0">
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="hidden" name="featured" value="0">
                <input type="checkbox" name="featured" id="featured" value="1"
                       {{ old('featured', $gallery->featured ?? false) ? 'checked' : '' }}
                       class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                <label for="featured" class="text-sm text-zinc-700">Featured — shows prominently on the public gallery page</label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                {{ isset($gallery) ? 'Update Image' : 'Add Image' }}
            </button>
            <a href="{{ route('admin.gallery.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
        </div>

    </div>
</form>
@endsection
