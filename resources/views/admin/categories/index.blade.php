@extends('layouts.admin')
@section('page_title', 'Categories')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Category</a>
</div>

<div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                <th class="px-6 py-3 font-medium">Name</th>
                <th class="px-6 py-3 font-medium">Slug</th>
                <th class="px-6 py-3 font-medium text-center">Posts</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($categories as $cat)
                <tr class="hover:bg-zinc-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color }}"></span>
                            <span class="font-medium text-zinc-900">{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-zinc-400 text-xs">{{ $cat->slug }}</td>
                    <td class="px-6 py-4 text-center text-zinc-500">{{ $cat->posts_count }}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="text-indigo-600 hover:text-indigo-800 mr-3 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete category?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-400">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
