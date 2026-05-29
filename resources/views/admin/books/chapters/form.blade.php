@extends('layouts.admin')
@section('page_title', isset($chapter) ? 'Edit Chapter' : 'New Chapter')

@section('content')
<div class="max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-zinc-400 mb-6">
        <a href="{{ route('admin.books.index') }}" class="hover:text-zinc-600">Books</a>
        <span>/</span>
        <a href="{{ route('admin.books.chapters.index', $book) }}" class="hover:text-zinc-600">{{ $book->title }}</a>
        <span>/</span>
        <span class="text-zinc-600">{{ isset($chapter) ? $chapter->title : 'New Chapter' }}</span>
    </div>

    <form method="POST"
        action="{{ isset($chapter)
            ? route('admin.books.chapters.update', [$book, $chapter])
            : route('admin.books.chapters.store', $book) }}"
        class="space-y-5">
        @csrf
        @if(isset($chapter)) @method('PUT') @endif

        <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="admin-label">Chapter Title</label>
                    <input type="text" name="title" value="{{ old('title', $chapter->title ?? '') }}" required class="admin-input" placeholder="e.g. Introduction, Part I: The Foundation…">
                    @error('title')<p class="admin-error">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="admin-label">Subtitle <span class="text-zinc-400 font-normal">— optional</span></label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $chapter->subtitle ?? '') }}" class="admin-input" placeholder="Optional chapter subtitle">
                </div>
            </div>

            <div>
                <label class="admin-label">Content <span class="text-zinc-400 font-normal">(HTML)</span></label>
                <textarea name="content" rows="28" class="admin-input font-mono text-sm resize-y"
                    placeholder="Write or paste chapter content here…">{{ old('content', $chapter->content ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 pt-2 border-t border-zinc-100">
                <div>
                    <label class="admin-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="draft"     {{ old('status', $chapter->status ?? 'draft') === 'draft'    ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $chapter->status ?? '') === 'published'     ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">Slug <span class="text-zinc-400 font-normal">— auto</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $chapter->slug ?? '') }}" class="admin-input font-mono text-sm" placeholder="auto-generated">
                    @error('slug')<p class="admin-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $chapter->sort_order ?? 0) }}" class="admin-input" min="0">
                </div>
            </div>

        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                {{ isset($chapter) ? 'Update Chapter' : 'Save Chapter' }}
            </button>
            <a href="{{ route('admin.books.chapters.index', $book) }}"
                class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
