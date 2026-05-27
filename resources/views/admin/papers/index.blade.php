@extends('layouts.admin')
@section('page_title', 'Papers')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.papers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ New Paper</a>
</div>

<div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wider text-zinc-400">
                <th class="px-6 py-3 font-medium">Title</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium hidden lg:table-cell">Published</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($papers as $paper)
                <tr class="hover:bg-zinc-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 truncate max-w-sm">{{ $paper->title }}</p>
                        @if($paper->pdf_url)<span class="text-xs text-zinc-400">PDF available</span>@endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $paper->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">{{ $paper->status }}</span>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell text-zinc-400">{{ $paper->published_at?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.papers.edit', $paper) }}" class="text-indigo-600 hover:text-indigo-800 mr-3 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.papers.destroy', $paper) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-400">No papers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
