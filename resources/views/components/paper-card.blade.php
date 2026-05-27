@props(['paper'])

<article class="flex flex-col gap-3 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-600 bg-white dark:bg-zinc-900 transition group">
    {{-- Categories --}}
    @if($paper->categories->isNotEmpty())
        <div class="flex flex-wrap gap-1.5">
            @foreach($paper->categories->take(2) as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                    class="text-xs font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full transition"
                    style="background-color: {{ $cat->color }}18; color: {{ $cat->color }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    @endif

    <h3 class="font-bold text-lg leading-snug font-serif">
        <a href="{{ route('papers.show', $paper->slug) }}" class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
            {{ $paper->title }}
        </a>
    </h3>

    @if($paper->abstract)
        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed line-clamp-3">{{ Str::limit($paper->abstract, 200) }}</p>
    @elseif($paper->excerpt)
        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed line-clamp-3">{{ $paper->excerpt }}</p>
    @endif

    <div class="flex items-center justify-between mt-auto pt-2">
        <div class="flex items-center gap-3 text-xs text-zinc-400">
            @if($paper->published_at)
                <time datetime="{{ $paper->published_at->toDateString() }}">{{ $paper->published_at->format('M j, Y') }}</time>
                <span>·</span>
            @endif
            <span>{{ $paper->reading_time }}</span>
        </div>
        @if($paper->pdf_url)
            <a href="{{ $paper->pdf_url }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
        @endif
    </div>
</article>
