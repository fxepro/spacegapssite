@extends('layouts.admin')
@section('page_title', 'Tags')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Add tag form --}}
    <div>
        <div class="bg-white rounded-xl border border-zinc-200 p-6">
            <h2 class="text-sm font-semibold text-zinc-700 mb-4">Add New Tag</h2>
            <form method="POST" action="{{ route('admin.tags.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" placeholder="Tag name" required class="admin-input flex-1">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition whitespace-nowrap">Add Tag</button>
            </form>
            @error('name')<p class="admin-error mt-2">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Tags list --}}
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                    <th class="px-5 py-3 font-medium">Tag</th>
                    <th class="px-5 py-3 font-medium text-center">Posts</th>
                    <th class="px-5 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($tags as $tag)
                    <tr class="hover:bg-zinc-50">
                        <td class="px-5 py-3 font-medium text-zinc-800">{{ $tag->name }}</td>
                        <td class="px-5 py-3 text-center text-zinc-400">{{ $tag->posts_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="inline" onsubmit="return confirm('Delete tag?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-zinc-400">No tags yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
