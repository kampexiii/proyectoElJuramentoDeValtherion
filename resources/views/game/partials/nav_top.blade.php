<nav class="navbar navbar-game navbar-dark bg-dark border-bottom border-secondary">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Izquierda: Tienda | Inventario -->
        <div class="d-flex gap-3">
            <a href="{{ route('game.tienda') }}" class="text-white fs-4" title="Tienda">
                <i class="bi bi-bag"></i>
            </a>
            <a href="{{ route('game.inventario') }}" class="text-white fs-4" title="Inventario">
                <i class="bi bi-backpack"></i>
            </a>
        </div>

        <!-- Centro: Logo -->
        <div class="position-absolute start-50 translate-middle-x">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.login') }}">
                        <img src="{{ asset('assets/brand/logo.png') }}" alt="Valtherion" class="nav-top-logo">
                    </a>
                @else
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/brand/logo.png') }}" alt="Valtherion" class="nav-top-logo">
                    </a>
                @endif
            @else
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/brand/logo.png') }}" alt="Valtherion" class="nav-top-logo">
                </a>
            @endauth
        </div>

        <!-- Derecha: Perfil | Ajustes -->
        <div class="d-flex gap-3">
            <button id="themeToggle" type="button" class="btn btn-sm btn-outline-secondary" title="Cambiar tema">
                <i class="bi bi-moon-stars"></i>
            </button>
            <a href="{{ route('game.perfil') }}" class="text-white fs-4" title="Perfil">
                <i class="bi bi-person"></i>
            </a>
            <a href="{{ route('game.ajustes') }}" class="text-white fs-4" title="Ajustes">
                <i class="bi bi-gear"></i>
            </a>
        </div>
    </div>
</nav>
