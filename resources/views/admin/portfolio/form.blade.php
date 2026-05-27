@extends('layouts.admin')
@section('page_title', isset($portfolioItem) ? 'Edit Portfolio Item' : 'New Portfolio Item')

@section('content')
<form method="POST" action="{{ isset($portfolioItem) ? route('admin.portfolio.update', $portfolioItem) : route('admin.portfolio.store') }}" class="max-w-4xl">
    @csrf
    @if(isset($portfolioItem)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
                <div>
                    <label class="admin-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $portfolioItem->title ?? '') }}" required class="admin-input">
                    @error('title')<p class="admin-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $portfolioItem->slug ?? '') }}" class="admin-input font-mono">
                </div>
                <div>
                    <label class="admin-label">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="admin-input resize-none">{{ old('excerpt', $portfolioItem->excerpt ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Content <span class="text-zinc-400 font-normal">(Markdown / HTML)</span></label>
                    <textarea name="content" rows="16" class="admin-input font-mono text-sm resize-y">{{ old('content', $portfolioItem->content ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Gallery Images <span class="text-zinc-400 font-normal">(one URL per line)</span></label>
                    <textarea name="gallery_raw" rows="5" class="admin-input font-mono text-sm resize-y" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg">{{ old('gallery_raw', isset($portfolioItem) ? implode("\n", $portfolioItem->gallery ?? []) : '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Publish</h3>
                <div>
                    <label class="admin-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="draft" {{ old('status', $portfolioItem->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $portfolioItem->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="featured" value="0">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $portfolioItem->featured ?? false) ? 'checked' : '' }}
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="featured" class="text-sm text-zinc-700">Featured</label>
                </div>
                <div>
                    <label class="admin-label">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $portfolioItem->sort_order ?? 0) }}" class="admin-input">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">{{ isset($portfolioItem) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.portfolio.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Project Details</h3>
                <div>
                    <label class="admin-label">Client</label>
                    <input type="text" name="client" value="{{ old('client', $portfolioItem->client ?? '') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Role</label>
                    <input type="text" name="role" value="{{ old('role', $portfolioItem->role ?? '') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Project Date</label>
                    <input type="date" name="project_date" value="{{ old('project_date', isset($portfolioItem->project_date) ? $portfolioItem->project_date->format('Y-m-d') : '') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">External URL</label>
                    <input type="url" name="external_url" value="{{ old('external_url', $portfolioItem->external_url ?? '') }}" class="admin-input" placeholder="https://…">
                </div>
                <div>
                    <label class="admin-label">Featured Image URL</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image', $portfolioItem->featured_image ?? '') }}" class="admin-input">
                </div>
            </div>

            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Categories</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ in_array($cat->id, old('categories', isset($portfolioItem) ? $portfolioItem->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-zinc-700">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
