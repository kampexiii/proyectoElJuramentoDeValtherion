{{-- HERO --}}
<section id="hero" class="mb-5 position-relative overflow-hidden rounded border border-secondary text-center py-4 py-md-5">

    {{-- Fondo (imagen + borde sutil + blur leve) --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 hero-bg-wrap" aria-hidden="true">
        <img
            src="{{ asset('assets/images/hero-bg.png') }}"
            alt=""
            class="hero-bg-img"
        />
    </div>

    {{-- Overlay oscuro (para legibilidad) --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 hero-overlay" aria-hidden="true"></div>

    {{-- Contenido --}}
    <div class="position-relative container" style="z-index: 2;">
        @include('partials.site_logo')

        <h1 class="display-5 display-md-4 text-white fw-bold hero-title">
            El Juramento de Valtherion
        </h1>

        <p class="lead text-white mb-4 fst-italic hero-subtitle">
            “Una crónica viva. Entras, eliges… y te marcas en la historia.”
        </p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg shadow-sm">
                Registrarse
            </a>

            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg shadow-sm btn-hero-access">
                Acceder
            </a>
        </div>
    </div>
</section>

