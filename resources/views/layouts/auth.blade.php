<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script>
        (function () {
            const key = 'valtherion_theme';
            const saved = localStorage.getItem(key);
            if (saved === 'light' || saved === 'dark') {
                document.documentElement.dataset.theme = saved;
            }
        })();
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @vite(['resources/css/theme.css'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current" />
            </a>
        </div>

        <button id="themeToggle" type="button" class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm">
            Tema
        </button>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-900 shadow-md overflow-hidden sm:rounded-lg border border-zinc-800">
            {{ $slot }}
        </div>
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
