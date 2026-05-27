@props(['item'])

<article class="group relative overflow-hidden rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition">
    @if($item->featured_image)
        <a href="{{ route('portfolio.show', $item->slug) }}" class="block overflow-hidden aspect-[4/3]">
            <img src="{{ $item->featured_image }}" alt="{{ $item->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </a>
    @else
        <a href="{{ route('portfolio.show', $item->slug) }}" class="block aspect-[4/3] bg-gradient-to-br from-indigo-100 to-violet-100 dark:from-indigo-900/30 dark:to-violet-900/30"></a>
    @endif

    <div class="p-5">
        {{-- Categories --}}
        @if($item->categories->isNotEmpty())
            <div class="flex flex-wrap gap-1.5 mb-2">
                @foreach($item->categories->take(2) as $cat)
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $cat->name }}</span>
                @endforeach
            </div>
        @endif

        <h3 class="font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition leading-snug">
            <a href="{{ route('portfolio.show', $item->slug) }}">{{ $item->title }}</a>
        </h3>

        @if($item->excerpt)
            <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $item->excerpt }}</p>
        @endif

        <div class="mt-3 flex items-center justify-between">
            @if($item->project_date)
                <span class="text-xs text-zinc-400">{{ $item->project_date->format('Y') }}</span>
            @endif
            @if($item->external_url)
                <a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View project →</a>
            @endif
        </div>
    </div>
</article>
