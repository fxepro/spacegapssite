@php
    $settings    = \App\Models\Settings::current();
    $navLinks    = $settings->visibleNavLinks();
    $profile     = \App\Models\Profile::current();
    $socialLinks = $profile->social_links ?? [];

    $socialIcons = [
        'linkedin'  => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z',
        'twitter'   => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z',
        'github'    => 'M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22',
        'youtube'   => 'M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z',
        'instagram' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4c0 3.2-2.6 5.8-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2z',
        'facebook'  => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
        'substack'  => 'M22 6H2v2h20V6zM2 10v8l10-5 10 5v-8H2zM2 4h20v2H2z',
        'website'   => 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14',
    ];
@endphp

<header x-data="{
    mobileOpen: false,
    masthead: true,
    lastY: 0,
    onScroll() {
        const y = window.scrollY;
        this.masthead = y < 60 || y < this.lastY;
        this.lastY = y;
    }
}" @scroll.window.passive="onScroll()" class="font-display border-b-2 border-sg-ink dark:border-sg-paper bg-sg-paper dark:bg-sg-ink sticky top-0 z-50">

    {{-- Top bar --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 h-11 flex items-center gap-0">

        {{-- Date --}}
        <span class="text-[11px] font-bold uppercase tracking-widest text-sg-muted pr-5 border-r border-sg-rule dark:border-sg-rule-dark whitespace-nowrap hidden sm:block">
            {{ now()->format('M j, Y') }}
        </span>

        {{-- Nav links (Settings-driven) --}}
        <nav class="hidden md:flex items-center h-full flex-1">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="h-full px-4 flex items-center text-[11px] font-extrabold uppercase tracking-widest border-r border-sg-rule dark:border-sg-rule-dark transition
                          {{ request()->routeIs($link['route']) ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper hover:bg-sg-rule/30 dark:hover:bg-sg-rule-dark/40' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Right: social icons + search + dark mode --}}
        <div class="flex items-center gap-0 ml-auto">

            {{-- Social links --}}
            @foreach($socialLinks as $social)
                @php $icon = $socialIcons[$social['platform']] ?? $socialIcons['website']; @endphp
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                   class="h-11 px-3 hidden lg:flex items-center border-l border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper hover:bg-sg-rule/30 dark:hover:bg-sg-rule-dark/40 transition"
                   title="{{ ucfirst($social['platform']) }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                </a>
            @endforeach

            <a href="{{ route('search') }}"
               class="h-11 px-4 flex items-center border-l border-sg-rule dark:border-sg-rule-dark text-[11px] font-extrabold uppercase tracking-widest text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper hover:bg-sg-rule/30 dark:hover:bg-sg-rule-dark/40 transition">
                Search
            </a>

            <button @click="dark = !dark"
                    class="h-11 px-4 flex items-center border-l border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper hover:bg-sg-rule/30 dark:hover:bg-sg-rule-dark/40 transition"
                    :title="dark ? 'Light mode' : 'Dark mode'">
                <svg x-show="!dark" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
                <svg x-show="dark" x-cloak class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </button>

            {{-- Mobile hamburger --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden h-11 px-4 flex items-center border-l border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile dropdown --}}
    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-sg-rule dark:border-sg-rule-dark">
        @foreach($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="block px-6 py-3 text-[11px] font-extrabold uppercase tracking-widest border-b border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:bg-sg-rule/20 transition">
                {{ $link['label'] }}
            </a>
        @endforeach
        {{-- Social links on mobile --}}
        @foreach($socialLinks as $social)
            <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
               class="block px-6 py-3 text-[11px] font-extrabold uppercase tracking-widest border-b border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:bg-sg-rule/20 transition">
                {{ ucfirst($social['platform']) }} ↗
            </a>
        @endforeach
    </div>

    {{-- Masthead --}}
    <div class="border-t border-sg-rule dark:border-sg-rule-dark text-center overflow-hidden transition-all duration-300 ease-in-out"
         :class="masthead ? 'max-h-48 py-6 sm:py-8 opacity-100' : 'max-h-0 py-0 opacity-0'">
        <a href="{{ route('home') }}" class="inline-block group">
            <div class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-sg-ink dark:text-sg-paper leading-none group-hover:opacity-75 transition">
                SpaceGaps
            </div>
            <p class="mt-2 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.35em] text-sg-muted">
                Writing &nbsp;·&nbsp; Research &nbsp;·&nbsp; Ideas
            </p>
        </a>
    </div>

</header>
