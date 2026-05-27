@extends('layouts.app')

@section('title', 'Contact')
@section('meta_description', 'Get in touch with SpaceGaps.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <h1 class="text-2xl font-extrabold mb-4">Contact</h1>
    <p class="text-zinc-500 dark:text-zinc-400 mb-10">Have a question, idea, or just want to say hello?</p>

    @if(session('success'))
        <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="form-input @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="form-input @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Message</label>
            <textarea name="message" id="message" rows="6" required
                class="form-input @error('message') border-red-400 @enderror resize-none">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary w-full">Send message</button>
    </form>
</div>
@endsection
