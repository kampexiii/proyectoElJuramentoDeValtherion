<section id="hero" class="mb-5 text-center p-5 hero-bg-wrap position-relative">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <img src="{{ asset('assets/images/hero-bg.jpg') }}" alt="Hero Background" class="hero-bg-img position-absolute top-0 start-0">
    <div class="position-relative z-index-1">
        <h1 class="display-4 fw-bold hero-title">El Juramento de Valtherion</h1>
        <p class="lead hero-subtitle mb-4">Esta sección será el Hero: gancho + botones</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Registrarse</a>
            <a href="{{ route('login') }}" class="btn btn-lg btn-hero-access">Acceder</a>
        </div>
    </div>
</section>
