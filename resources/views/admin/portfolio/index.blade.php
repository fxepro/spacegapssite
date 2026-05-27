@extends('layouts.admin')
@section('page_title', 'Portfolio')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.portfolio.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Item</a>
</div>

<div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                <th class="px-6 py-3 font-medium">Title</th>
                <th class="px-6 py-3 font-medium hidden md:table-cell">Client</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($items as $item)
                <tr class="hover:bg-zinc-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 truncate max-w-xs">{{ $item->title }}</p>
                        @if($item->featured)<span class="text-xs text-amber-500">★ Featured</span>@endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-zinc-500">{{ $item->client ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">{{ $item->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('portfolio.show', $item->slug) }}" target="_blank" class="text-zinc-400 hover:text-zinc-600 mr-3 text-xs">View</a>
                        <a href="{{ route('admin.portfolio.edit', $item) }}" class="text-indigo-600 hover:text-indigo-800 mr-3 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.portfolio.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-400">No portfolio items yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($items->hasPages())<div class="px-6 py-4 border-t border-zinc-100">{{ $items->links() }}</div>@endif
</div>
@endsection
