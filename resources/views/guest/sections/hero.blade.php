<section id="hero" class="mb-5 text-center p-5 hero-bg-wrap position-relative">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <img src="{{ asset('assets/images/hero-bg.jpg') }}" alt="Hero Background" class="hero-bg-img position-absolute top-0 start-0">
    <div class="position-relative z-index-1">
        <h1 class="display-4 fw-bold hero-title">El Juramento de Valtherion</h1>
        <p class="lead hero-subtitle mb-2">“Toda historia de ida, tiene un camino de vuelta...”</p>
        <p class="hero-quote mb-4">↑ Volver al Inicio para Realizar el Juramento ↑</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Registrarse</a>
            <a href="{{ route('login') }}" class="btn btn-lg btn-hero-access">Acceder</a>
        </div>
    </div>
</section>
