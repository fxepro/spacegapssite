@extends('layouts.admin')
@section('page_title', isset($category) ? 'Edit Category' : 'New Category')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div>
            <label class="admin-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="admin-input">
            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="admin-label">Description</label>
            <textarea name="description" rows="3" class="admin-input resize-none">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="admin-label">Color</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" value="{{ old('color', $category->color ?? '#6366f1') }}"
                    class="h-10 w-16 rounded-lg border border-zinc-300 cursor-pointer p-1">
                <input type="text" id="color_text" value="{{ old('color', $category->color ?? '#6366f1') }}"
                    class="admin-input flex-1 font-mono" placeholder="#6366f1"
                    oninput="document.querySelector('[name=color]').value = this.value">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                {{ isset($category) ? 'Update' : 'Create' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-zinc-300 text-zinc-600 hover:bg-zinc-50 rounded-lg text-sm transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
