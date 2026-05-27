@props(['post', 'featured' => false, 'horizontal' => false])

<article class="{{ $horizontal ? 'flex gap-4' : 'flex flex-col' }} group">
    @if($post->featured_image && !$horizontal)
        <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden {{ $featured ? 'aspect-video' : 'aspect-[4/3]' }} bg-sg-rule/20 dark:bg-sg-rule-dark/30 mb-4">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
        </a>
    @elseif($post->featured_image && $horizontal)
        <a href="{{ route('blog.show', $post->slug) }}" class="flex-shrink-0 w-24 h-24 overflow-hidden bg-sg-rule/20 dark:bg-sg-rule-dark/30">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </a>
    @endif

    <div class="flex flex-col flex-1 gap-1.5">
        @if($post->categories->isNotEmpty())
            <a href="{{ route('categories.show', $post->categories->first()->slug) }}"
               class="text-[10px] font-bold uppercase tracking-widest transition"
               style="color: {{ $post->categories->first()->color }}">
                {{ $post->categories->first()->name }}
            </a>
        @endif

        <h3 class="{{ $featured ? 'text-xl' : 'text-base' }} font-display font-bold leading-snug">
            <a href="{{ route('blog.show', $post->slug) }}" class="hover:opacity-70 transition {{ $featured ? '' : 'line-clamp-3' }}">
                {{ $post->title }}
            </a>
        </h3>

        @if($featured && $post->excerpt)
            <p class="text-sm text-sg-body dark:text-sg-paper/60 font-serif leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
        @endif

        <p class="text-[11px] text-sg-muted mt-1">
            @if($post->published_at)<time>{{ $post->published_at->format('M j, Y') }}</time> · @endif{{ $post->reading_time }}
        </p>
    </div>
</article>
