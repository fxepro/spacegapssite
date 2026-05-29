@extends('layouts.admin')
@section('page_title', isset($paper) ? 'Edit Paper' : 'New Paper')

@section('content')
<form method="POST" action="{{ isset($paper) ? route('admin.papers.update', $paper) : route('admin.papers.store') }}" class="max-w-4xl">
    @csrf
    @if(isset($paper)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main column ──────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Core content --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
                <div>
                    <label class="admin-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $paper->title ?? '') }}" required class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Abstract <span class="text-zinc-400 font-normal">— concise summary</span></label>
                    <textarea name="abstract" rows="4" class="admin-input resize-none" placeholder="A concise summary of the paper's argument and findings.">{{ old('abstract', $paper->abstract ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Content <span class="text-zinc-400 font-normal">(HTML)</span></label>
                    <textarea name="content" rows="22" class="admin-input font-mono text-sm resize-y">{{ old('content', $paper->content ?? '') }}</textarea>
                </div>
            </div>

            {{-- Citations builder --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6"
                 x-data="{
                    citations: {{ json_encode(old('citations_data', isset($paper) ? ($paper->citations ?? []) : [])) }},
                    add()  { this.citations.push({ text: '', url: '' }) },
                    remove(i) { this.citations.splice(i, 1) }
                 }">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-zinc-700">Citations &amp; References</h3>
                    <button type="button" @click="add()"
                        class="text-xs px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-medium transition">
                        + Add Citation
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(c, i) in citations" :key="i">
                        <div class="flex gap-2 items-start group">
                            <span class="mt-2 text-xs text-zinc-400 font-mono w-5 shrink-0 text-right" x-text="i + 1"></span>
                            <div class="flex-1 space-y-1.5">
                                <input type="text"
                                    x-model="c.text"
                                    placeholder="Author(s), Year. Title. Journal/Source."
                                    class="admin-input text-sm">
                                <input type="url"
                                    x-model="c.url"
                                    placeholder="https://doi.org/… (optional)"
                                    class="admin-input text-sm font-mono">
                            </div>
                            <button type="button" @click="remove(i)"
                                class="mt-2 text-zinc-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <p x-show="citations.length === 0" class="text-sm text-zinc-400 text-center py-4">No citations yet. Click + Add Citation to start.</p>

                {{-- Hidden JSON payload --}}
                <input type="hidden" name="citations_json" :value="JSON.stringify(citations)">
            </div>

            {{-- Plain references / bibliography (optional freeform) --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6">
                <label class="admin-label">Bibliography <span class="text-zinc-400 font-normal">— freeform text, one entry per line (optional)</span></label>
                <textarea name="references" rows="5" class="admin-input font-mono text-sm resize-y" placeholder="[1] Smith, J. et al. (2024)…">{{ old('references', $paper->references ?? '') }}</textarea>
            </div>

        </div>

        {{-- ── Sidebar ──────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Publish --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700">Publish</h3>
                <div>
                    <label class="admin-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="draft"     {{ old('status', $paper->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $paper->status ?? '')     === 'published'  ? 'selected' : '' }}>Published</option>
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
                    <input type="checkbox" name="featured" id="featured" value="1"
                        {{ old('featured', $paper->featured ?? false) ? 'checked' : '' }}
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="featured" class="text-sm text-zinc-700">Featured</label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                        {{ isset($paper) ? 'Update Paper' : 'Create Paper' }}
                    </button>
                    <a href="{{ route('admin.papers.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
                </div>
            </div>

            {{-- Meta --}}
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
                    <textarea name="excerpt" rows="3" class="admin-input resize-none" placeholder="Short description for cards and SEO…">{{ old('excerpt', $paper->excerpt ?? '') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">PDF Download URL</label>
                    <input type="url" name="pdf_url" value="{{ old('pdf_url', $paper->pdf_url ?? '') }}" class="admin-input font-mono text-sm" placeholder="https://…">
                </div>
                <div>
                    <label class="admin-label">Featured Image URL</label>
                    <input type="url" name="featured_image" value="{{ old('featured_image', $paper->featured_image ?? '') }}" class="admin-input font-mono text-sm" placeholder="https://…">
                </div>
            </div>

            {{-- Gallery --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Image Gallery</h3>
                <textarea name="gallery_raw" rows="5" class="admin-input font-mono text-sm resize-y"
                    placeholder="One image URL per line&#10;https://…&#10;https://…">{{ old('gallery_raw', isset($paper) ? implode("\n", $paper->gallery ?? []) : '') }}</textarea>
                <p class="text-xs text-zinc-400 mt-1.5">One URL per line. Images appear below the content.</p>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Categories</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
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

            {{-- Tags --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 mb-3">Tags</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', isset($paper) ? $paper->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
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
