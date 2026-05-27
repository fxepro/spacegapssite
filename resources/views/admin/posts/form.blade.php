@extends('layouts.admin')
@section('page_title', isset($post) ? 'Edit Post' : 'New Post')

@section('content')
<form method="POST" action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="max-w-4xl">
    @csrf
    @if(isset($post)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
                <div>
                    <label class="admin-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required
                        class="admin-input" placeholder="Post title">
                    @error('title')<p class="admin-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="admin-label">Slug <span class="text-zinc-400 font-normal">(auto-generated if blank)</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}"
                        class="admin-input font-mono" placeholder="url-friendly-slug">
                    @error('slug')<p class="admin-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="admin-label">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="admin-input resize-none" placeholder="Short description (shown in cards and meta)">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                </div>

                <div>
                    <label class="admin-label">Content <span class="text-zinc-400 font-normal">(Markdown / HTML supported)</span></label>
                    <textarea name="content" rows="20" class="admin-input font-mono text-sm resize-y" placeholder="Write your post content here…">{{ old('content', $post->content ?? '') }}</textarea>
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
                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="admin-label">Publish Date</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', isset($post->published_at) ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="admin-input">
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="featured" value="0">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $post->featured ?? false) ? 'checked' : '' }}
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="featured" class="text-sm text-zinc-700">Featured post</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                        {{ isset($post) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
                </div>
            </div>

            {{-- Meta --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Meta</h3>
                <div>
                    <label class="admin-label">Author</label>
                    <input type="text" name="author" value="{{ old('author', $post->author ?? 'Admin') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Featured Image URL</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image ?? '') }}" class="admin-input" placeholder="https://…">
                </div>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Categories</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ in_array($cat->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
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
                                {{ in_array($tag->id, old('tags', isset($post) ? $post->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-zinc-700">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
