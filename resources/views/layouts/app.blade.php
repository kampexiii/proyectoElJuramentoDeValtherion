<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            (function () {
                const key = 'valtherion_theme';
                const saved = localStorage.getItem(key);
                if (saved === 'light' || saved === 'dark') {
                    document.documentElement.dataset.theme = saved;
                }
            })();
        </script>

        <!-- CSS/JS zona logueada -->
        @vite(['resources/css/game/app.css', 'resources/js/game/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="v-surface-nav">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            (function () {
                const root = document.documentElement;
                const key = 'valtherion_theme';

                const btn = document.getElementById('themeToggle');
                if (!btn) return;

                btn.addEventListener('click', () => {
                    const next = (root.dataset.theme === 'dark') ? 'light' : 'dark';
                    root.dataset.theme = next;
                    localStorage.setItem(key, next);
                });
            })();
        </script>
    </body>
</html>
