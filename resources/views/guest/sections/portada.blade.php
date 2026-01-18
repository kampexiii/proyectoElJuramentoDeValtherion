<section id="hero" class="mb-5 text-center p-5 border border-secondary rounded position-relative overflow-hidden" style="background-image: url('{{ asset('assets/images/hero-bg.png') }}'); background-size: cover; background-position: center;">
    <!-- Overlay con Blur -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 1;"></div>
    
    <!-- Contenido (encima del blur) -->
    <div class="position-relative" style="z-index: 2;">
        @include('partials.site_logo')
        <h1 class="display-4 text-white fw-bold" style="font-family: serif; text-shadow: 2px 2px 4px #000;">El Juramento de Valtherion</h1>
        <p class="lead text-white mb-4 fst-italic" style="text-shadow: 1px 1px 2px #000;">“Una crónica viva. Entras, eliges… y te marcas en la historia.”</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg shadow-sm">Registrarse</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg shadow-sm">Acceder</a>
        </div>
    </div>
</section>
