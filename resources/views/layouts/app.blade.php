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
</body>
</html>
