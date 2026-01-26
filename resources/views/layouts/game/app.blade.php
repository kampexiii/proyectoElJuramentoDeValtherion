<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name', 'Valtherion') }}</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
    
    <style>
        html, body {
            height: 100dvh; /* Usamos dvh para navegadores móviles modernos */
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: var(--bg-app);
            color: var(--text-main);
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            overflow: hidden; /* Evita scroll absoluto, el contenido debe ajustarse */
            display: flex;
            flex-direction: column;
            padding: 1rem;
            position: relative;
        }
        .navbar-game {
            flex-shrink: 0;
            height: 64px;
            z-index: 1030;
        }
    </style>
</head>
<body>

    @include('game.partials.nav_top')

    <main id="game-container">
        @yield('content')
    </main>

    @include('game.partials.nav_bottom')

    <!-- Bootstrap JS Bundle CDN -->
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
