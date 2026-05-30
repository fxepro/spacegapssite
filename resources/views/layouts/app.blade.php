<!DOCTYPE html>
<html lang="en" x-data="{ dark: localStorage.getItem('sg-theme') === 'dark' }"
      :class="{ 'dark': dark }"
      x-init="$watch('dark', v => localStorage.setItem('sg-theme', v ? 'dark' : 'light'))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SpaceGaps')</title>
    <meta name="description" content="@yield('meta_description', 'SpaceGaps — writing, research, and ideas on technology, society, and the world.')">

    <meta property="og:title"       content="@yield('title', 'SpaceGaps')">
    <meta property="og:description" content="@yield('meta_description', 'SpaceGaps — writing, research, and ideas.')">
    <meta property="og:image"       content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta name="twitter:card"       content="summary_large_image">

    @yield('schema')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;1,8..60,300;1,8..60,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
</head>
<body class="bg-sg-paper dark:bg-sg-ink text-sg-ink dark:text-sg-paper antialiased transition-colors duration-200 min-h-screen flex flex-col">

    @include('components.nav')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer')

    @stack('scripts')

    {{-- ── Global Lightbox Overlay ───────────────────────────────────────── --}}
    <div x-data
         x-show="$store.lb.open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="$store.lb.close()"
         @keydown.arrow-left.window="$store.lb.prev()"
         @keydown.arrow-right.window="$store.lb.next()"
         @click.self="$store.lb.close()"
         class="fixed inset-0 z-[200] bg-black/95 flex flex-col items-center justify-center select-none"
         role="dialog" aria-modal="true">

        {{-- Close --}}
        <button @click="$store.lb.close()"
            class="absolute top-4 right-4 text-white/50 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-10"
            aria-label="Close">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Prev --}}
        <button @click="$store.lb.prev()"
            x-show="$store.lb.images.length > 1"
            class="absolute left-3 sm:left-6 text-white/50 hover:text-white p-3 rounded-full hover:bg-white/10 transition"
            aria-label="Previous">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Image or Video --}}
        <template x-if="$store.lb.img.type === 'video'">
            <iframe :src="$store.lb.img.url"
                    class="w-[88vw] max-w-4xl aspect-video rounded-lg shadow-2xl"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    @click.stop></iframe>
        </template>
        <template x-if="$store.lb.img.type !== 'video'">
            <img :src="$store.lb.img.url"
                 :alt="$store.lb.img.title || 'Image'"
                 class="max-h-[80vh] max-w-[88vw] object-contain rounded-lg shadow-2xl"
                 @click.stop>
        </template>

        {{-- Caption bar --}}
        <div class="mt-4 text-center px-12 min-h-[3rem]">
            <p x-show="$store.lb.img.title"
               x-text="$store.lb.img.title"
               class="text-white text-sm font-semibold leading-snug"></p>
            <p x-show="$store.lb.img.caption"
               x-text="$store.lb.img.caption"
               class="text-white/55 text-sm mt-0.5 leading-snug"></p>
            <p x-show="$store.lb.images.length > 1"
               x-text="`${$store.lb.idx + 1} / ${$store.lb.images.length}`"
               class="text-white/30 text-xs mt-1.5 font-mono"></p>
        </div>

        {{-- Next --}}
        <button @click="$store.lb.next()"
            x-show="$store.lb.images.length > 1"
            class="absolute right-3 sm:right-6 text-white/50 hover:text-white p-3 rounded-full hover:bg-white/10 transition"
            aria-label="Next">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

</body>
</html>
