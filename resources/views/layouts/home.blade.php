<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Valtherion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        (function () {
            const key = 'valtherion_theme';
            const saved = localStorage.getItem(key);
            if (saved === 'light' || saved === 'dark') {
                document.documentElement.dataset.theme = saved;
            }
        })();
    </script>

    @vite(['resources/css/theme.css'])
</head>
<body>
    <header class="v-surface-nav p-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">Home del juego</h1>
            @auth
            <div class="d-flex align-items-center gap-3">
                <span>Hola, {{ auth()->user()->name }}</span>
                <button id="themeToggle" type="button" class="btn btn-sm btn-outline-secondary">Tema</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Salir</button>
                </form>
            </div>
            @endauth
        </div>
    </header>
    <main class="container">
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
