@extends('layouts.admin')
@section('page_title', 'Settings — Security & Visibility')

@section('content')
<div class="max-w-2xl">

    <form method="POST" action="{{ route('admin.settings.security.update') }}">
        @csrf @method('PUT')

        {{-- Nav Visibility --}}
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-zinc-100">
                <h2 class="text-sm font-bold text-zinc-800">Public Navigation</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Toggle which pages appear in the top navigation bar. Hiding a page removes it from the nav but the URL still works.</p>
            </div>

            @php
            $pages = [
                ['key' => 'nav_home',      'label' => 'Home',      'route' => 'home',          'desc' => 'The homepage'],
                ['key' => 'nav_blog',      'label' => 'Blog',      'route' => 'blog.index',    'desc' => 'Posts and essays'],
                ['key' => 'nav_portfolio', 'label' => 'Portfolio', 'route' => 'portfolio.index','desc' => 'Projects and work'],
                ['key' => 'nav_papers',    'label' => 'Research',  'route' => 'papers.index',  'desc' => 'Research and academic writing'],
                ['key' => 'nav_gallery',   'label' => 'Gallery',   'route' => 'gallery.index', 'desc' => 'Photo gallery'],
                ['key' => 'nav_books',     'label' => 'Books',     'route' => 'books.index',   'desc' => 'Books and chapters'],
                ['key' => 'nav_videos',    'label' => 'Videos',    'route' => 'videos.index',  'desc' => 'Video library'],
                ['key' => 'nav_about',     'label' => 'Profile',   'route' => 'about',         'desc' => 'Public profile page — photo, intro, social links'],
                ['key' => 'nav_contact',   'label' => 'Contact',   'route' => 'contact',       'desc' => 'Contact form'],
            ];
            @endphp

            <div class="divide-y divide-zinc-100">
                @foreach($pages as $page)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-zinc-800">{{ $page['label'] }}</p>
                            <p class="text-xs text-zinc-400">{{ $page['desc'] }} · <a href="{{ route($page['route']) }}" target="_blank" class="hover:text-indigo-600 transition font-mono">/{{ $page['route'] === 'home' ? '' : str_replace('.index', '', str_replace('.', '/', $page['route'])) }}</a></p>
                        </div>
                        {{-- Toggle switch --}}
                        <label class="relative inline-flex items-center cursor-pointer" x-data="{ on: {{ $settings->{$page['key']} ? 'true' : 'false' }} }">
                            <input type="hidden" name="{{ $page['key'] }}" value="0">
                            <input type="checkbox" name="{{ $page['key'] }}" value="1"
                                   x-model="on"
                                   {{ $settings->{$page['key']} ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div @click="on = !on"
                                 :class="on ? 'bg-indigo-600' : 'bg-zinc-200'"
                                 class="w-11 h-6 rounded-full transition-colors duration-200 cursor-pointer">
                                <div :class="on ? 'translate-x-5' : 'translate-x-1'"
                                     class="absolute top-0.5 left-0 mt-0.5 ml-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"></div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
