<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row text-center">
            <!-- Logo y descripción -->
            <div class="col-md-4 mb-4">
                <h5 class="text-warning fw-bold mb-3">El Juramento de Valtherion</h5>
                <p class="small text-muted">Embárcate en una aventura épica donde tus decisiones forjan el destino del reino. Únete a la comunidad y realiza tu juramento.</p>
            </div>
            <!-- Enlaces rápidos -->
            <div class="col-md-4 mb-4">
                <h6 class="text-warning fw-bold mb-3 text-uppercase">Enlaces</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('register') }}" class="text-light text-decoration-none border-bottom border-secondary pb-1">Registrarse</a></li>
                    <li><a href="{{ route('login') }}" class="text-light text-decoration-none border-bottom border-secondary pb-1">Iniciar Sesión</a></li>
                    <li><a href="#hero" class="text-light text-decoration-none border-bottom border-secondary pb-1">Inicio</a></li>
                    <li><a href="#razas" class="text-light text-decoration-none border-bottom border-secondary pb-1">Razas</a></li>
                    <li><a href="#" class="text-light text-decoration-none border-bottom border-secondary pb-1">Legales</a></li>
                    <li><a href="https://amazon.com" class="text-light text-decoration-none border-bottom border-secondary pb-1">Libro</a></li>
                    <li><a href="#" class="text-light text-decoration-none border-bottom border-secondary pb-1">Lore</a></li>
                </ul>
            </div>
            <!-- Redes sociales -->
            <div class="col-md-4 mb-4">
                <h6 class="text-warning fw-bold mb-3 text-uppercase">Síguenos</h6>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-light" title="Discord"><i class="fab fa-discord fa-lg"></i></a>
                    <a href="#" class="text-light" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-light" title="YouTube"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row">
            <div class="col-12 text-center">
                <small class="text-muted">&copy; {{ date('Y') }} El Juramento de Valtherion. Todos los derechos reservados.</small>
            </div>
        </div>
    </div>
</footer>
