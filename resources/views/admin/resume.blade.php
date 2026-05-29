@extends('layouts.admin')
@section('page_title', 'Resume')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-zinc-200 p-12 text-center">
        <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-zinc-800 mb-2">Resume</h2>
        <p class="text-sm text-zinc-400 max-w-sm mx-auto">
            Planned feature — build, manage, and publish your résumé directly from SpaceGaps. Upload a PDF, edit sections, control visibility.
        </p>
        <span class="mt-6 inline-block text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full bg-zinc-100 text-zinc-400">
            Coming soon
        </span>
    </div>
</div>
@endsection
