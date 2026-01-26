<section id="hero" class="mb-5 position-relative overflow-hidden rounded border border-secondary text-center py-5">
    <!-- Imagen de fondo -->
    <img
        src="{{ asset('assets/images/hero-bg.png') }}"
        alt=""
        class="position-absolute top-0 start-0 w-100 h-100"
        style="object-fit: cover; z-index: 0;"
    />

    <!-- Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100 hero-overlay" style="z-index: 1;"></div>

    <!-- Contenido -->
    <div class="position-relative container" style="z-index: 2;">
        @include('partials.site_logo')

        <h1 class="display-5 display-md-4 text-white fw-bold hero-title">
            El Juramento de Valtherion
        </h1>

        <p class="lead text-white mb-4 fst-italic hero-subtitle">
            “Una crónica viva. Entras, eliges… y te marcas en la historia.”
        </p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg shadow-sm">Registrarse</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg shadow-sm btn-hero-access">Acceder</a>
        </div>
    </div>
</section>
