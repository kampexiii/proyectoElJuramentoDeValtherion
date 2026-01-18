<section id="hero" class="mb-5 text-center p-5 bg-black border border-secondary rounded">
    @include('partials.site_logo')
    <h1 class="display-4 text-warning fw-bold" style="font-family: serif;">El Juramento de Valtherion</h1>
    <p class="lead text-light mb-4 fst-italic">“Una crónica viva. Entras, eliges… y te marcas en la historia.”</p>
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Registrarse</a>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Acceder</a>
    </div>
</section>
