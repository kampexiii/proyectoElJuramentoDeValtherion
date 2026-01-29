<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CSS/JS zona logueada -->
    @vite(['resources/css/game/app.css', 'resources/js/game/app.js'])
</head>
<body class="antialiased auth-shell">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 auth-wrap">
        <div class="auth-logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-900 shadow-md overflow-hidden sm:rounded-lg border border-zinc-800 auth-card">
            {{ $slot }}
        </div>
    </div>

</body>
</html>
