<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name', 'Valtherion') }}</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #000;
            color: #fff;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            height: calc(100vh - 128px);
            overflow: hidden;
            padding: 1rem;
        }
        .navbar {
            z-index: 1030;
        }
    </style>
</head>
<body>

    @include('game.partials.nav_top')

    <main>
        @yield('content')
    </main>

    @include('game.partials.nav_bottom')

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
