<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — SpaceGaps Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-zinc-100 font-sans" x-data="{ sidebarOpen: false }">

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-zinc-900 text-zinc-100">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-zinc-800">
            <span class="text-xl font-bold tracking-tight text-white">SpaceGaps</span>
            <span class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full font-medium">Admin</span>
        </div>
        <nav class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            @include('admin.partials.sidebar-nav')
        </nav>
        <div class="px-4 py-4 border-t border-zinc-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-zinc-400 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition">
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen w-full min-w-0">
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between lg:px-8">
            <h1 class="text-sm font-extrabold text-zinc-800">@yield('page_title', 'Dashboard')</h1>
            <div class="flex items-center gap-4 text-sm text-zinc-500">
                <span>{{ auth()->user()->name ?? auth()->user()->email }}</span>
                <a href="{{ route('home') }}" target="_blank" class="text-indigo-600 hover:underline">View site</a>
            </div>
        </header>

        <main class="flex-1 p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
