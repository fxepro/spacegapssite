<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — SpaceGaps</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-zinc-50 flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold font-sans text-zinc-900">SpaceGaps</h1>
        <p class="mt-1 text-sm text-zinc-500">Admin panel</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 p-8">
        <h2 class="text-sm font-extrabold text-zinc-800 mb-6">Sign in</h2>

        @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2.5 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-zinc-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2.5 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="remember" id="remember" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="text-sm text-zinc-600">Remember me</label>
            </div>

            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition mt-2">
                Sign in
            </button>
        </form>
    </div>

    <p class="text-center mt-6">
        <a href="{{ route('home') }}" class="text-sm text-zinc-400 hover:text-zinc-600 transition">← Back to site</a>
    </p>
</div>

</body>
</html>
