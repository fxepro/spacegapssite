@extends('layouts.admin')
@section('page_title', isset($paper) ? 'Edit Paper' : 'New Paper')

@section('content')
<form method="POST" action="{{ isset($paper) ? route('admin.papers.update', $paper) : route('admin.papers.store') }}" class="max-w-4xl">
    @csrf
    @if(isset($paper)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
                <div>
                    <label class="admin-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $paper->title ?? '') }}" required class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Abstract</label>
                    <textarea name="abstract" rows="4" class="admin-input resize-none" placeholder="A concise summary of the paper's argument and findings.">{{ old('abstract', $paper->abstract ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Content <span class="text-zinc-400 font-normal">(Markdown / HTML)</span></label>
                    <textarea name="content" rows="20" class="admin-input font-mono text-sm resize-y">{{ old('content', $paper->content ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">References</label>
                    <textarea name="references" rows="6" class="admin-input font-mono text-sm resize-y" placeholder="One reference per line…">{{ old('references', $paper->references ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Publish</h3>
                <div>
                    <label class="admin-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="draft" {{ old('status', $paper->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $paper->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">Publish Date</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', isset($paper->published_at) ? $paper->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="admin-input">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="featured" value="0">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $paper->featured ?? false) ? 'checked' : '' }}
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="featured" class="text-sm text-zinc-700">Featured</label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">{{ isset($paper) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.papers.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Meta</h3>
                <div>
                    <label class="admin-label">Author</label>
                    <input type="text" name="author" value="{{ old('author', $paper->author ?? 'Admin') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $paper->slug ?? '') }}" class="admin-input font-mono">
                </div>
                <div>
                    <label class="admin-label">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="admin-input resize-none">{{ old('excerpt', $paper->excerpt ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">PDF URL</label>
                    <input type="text" name="pdf_url" value="{{ old('pdf_url', $paper->pdf_url ?? '') }}" class="admin-input" placeholder="https://…">
                </div>
                <div>
                    <label class="admin-label">Featured Image URL</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image', $paper->featured_image ?? '') }}" class="admin-input">
                </div>
            </div>

            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Categories</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ in_array($cat->id, old('categories', isset($paper) ? $paper->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
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
