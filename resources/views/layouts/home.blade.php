<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Valtherion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary text-light">
    <header class="bg-dark p-3 mb-4 border-bottom border-light">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">Home del juego</h1>
            @auth
            <div class="d-flex align-items-center gap-3">
                <span>Hola, {{ auth()->user()->name }}</span>
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
</body>
</html>
