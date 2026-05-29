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

    {{-- Single top bar: date · nav links · utilities --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 h-11 flex items-center gap-0">

        {{-- Date --}}
        <span class="text-[11px] font-bold uppercase tracking-widest text-sg-muted pr-5 border-r border-sg-rule dark:border-sg-rule-dark whitespace-nowrap hidden sm:block">
            {{ now()->format('M j, Y') }}
        </span>

        {{-- Nav links --}}
        <nav class="hidden md:flex items-center h-full flex-1">
            @foreach([
                ['Home',      'home'],
                ['Blog',      'blog.index'],
                ['Portfolio', 'portfolio.index'],
                ['Papers',    'papers.index'],
                ['Gallery',   'gallery.index'],
                ['About',     'about'],
                ['Contact',   'contact'],
            ] as [$label, $route])
                <a href="{{ route($route) }}"
                   class="h-full px-4 flex items-center text-[11px] font-extrabold uppercase tracking-widest border-r border-sg-rule dark:border-sg-rule-dark transition
                          {{ request()->routeIs($route) ? 'bg-sg-ink dark:bg-sg-paper text-sg-paper dark:text-sg-ink' : 'text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper hover:bg-sg-rule/30 dark:hover:bg-sg-rule-dark/40' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Right utilities --}}
        <div class="flex items-center gap-0 ml-auto">
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
        @foreach([['Home','home'],['Blog','blog.index'],['Portfolio','portfolio.index'],['Papers','papers.index'],['Gallery','gallery.index'],['About','about'],['Contact','contact']] as [$l,$r])
            <a href="{{ route($r) }}"
               class="block px-6 py-3 text-[11px] font-extrabold uppercase tracking-widest border-b border-sg-rule dark:border-sg-rule-dark text-sg-body dark:text-sg-paper/70 hover:bg-sg-rule/20 transition">
                {{ $l }}
            </a>
        @endforeach
    </div>

    {{-- Masthead — collapses on scroll down, returns on scroll up --}}
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
