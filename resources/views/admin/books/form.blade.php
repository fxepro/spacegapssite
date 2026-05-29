@extends('layouts.admin')
@section('page_title', isset($book) ? 'Edit Book' : 'New Book')

@section('content')
<form method="POST" action="{{ isset($book) ? route('admin.books.update', $book) : route('admin.books.store') }}" class="max-w-4xl">
    @csrf
    @if(isset($book)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
                <div>
                    <label class="admin-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $book->title ?? '') }}" required class="admin-input" placeholder="The title of your book">
                    @error('title')<p class="admin-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">Subtitle <span class="text-zinc-400 font-normal">— optional</span></label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $book->subtitle ?? '') }}" class="admin-input" placeholder="A clarifying subtitle">
                </div>
                <div>
                    <label class="admin-label">Description <span class="text-zinc-400 font-normal">— what this book is about</span></label>
                    <textarea name="description" rows="8" class="admin-input resize-y" placeholder="Full description, back-cover style…">{{ old('description', $book->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Excerpt <span class="text-zinc-400 font-normal">— short teaser for cards</span></label>
                    <textarea name="excerpt" rows="3" class="admin-input resize-none" placeholder="One or two sentences for the listing page…">{{ old('excerpt', $book->excerpt ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Publish --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Publish</h3>
                <div>
                    <label class="admin-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="draft"     {{ old('status', $book->status ?? 'draft') === 'draft'    ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $book->status ?? '') === 'published'     ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">Publish Date</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', isset($book->published_at) ? $book->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="admin-input">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="featured" value="0">
                    <input type="checkbox" name="featured" id="featured" value="1"
                        {{ old('featured', $book->featured ?? false) ? 'checked' : '' }}
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="featured" class="text-sm text-zinc-700">Featured</label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                        {{ isset($book) ? 'Update Book' : 'Create Book' }}
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
                </div>
                @if(isset($book))
                    <a href="{{ route('admin.books.chapters.index', $book) }}"
                        class="block text-center text-sm text-violet-600 hover:text-violet-800 font-medium pt-1">
                        → Manage Chapters ({{ $book->chapters()->count() }})
                    </a>
                @endif
            </div>

            {{-- Meta --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Meta</h3>
                <div>
                    <label class="admin-label">Author</label>
                    <input type="text" name="author" value="{{ old('author', $book->author ?? 'Admin') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $book->slug ?? '') }}" class="admin-input font-mono text-sm">
                </div>
                <div>
                    <label class="admin-label">Cover Image URL</label>
                    <input type="url" name="cover_image" value="{{ old('cover_image', $book->cover_image ?? '') }}" class="admin-input font-mono text-sm" placeholder="https://…">
                    @if(!empty($book->cover_image ?? null))
                        <img src="{{ $book->cover_image }}" class="mt-2 h-24 w-auto rounded-lg border border-zinc-200 object-cover" alt="Cover">
                    @endif
                </div>
                <div>
                    <label class="admin-label">Featured Image URL</label>
                    <input type="url" name="featured_image" value="{{ old('featured_image', $book->featured_image ?? '') }}" class="admin-input font-mono text-sm" placeholder="https://…">
                </div>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Categories</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ in_array($cat->id, old('categories', isset($book) ? $book->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-zinc-700">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Tags</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', isset($book) ? $book->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-zinc-700">#{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
