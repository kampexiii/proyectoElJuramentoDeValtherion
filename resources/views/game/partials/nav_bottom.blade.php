<nav class="navbar navbar-dark bg-dark border-top border-secondary fixed-bottom" style="height: 64px;">
    <div class="container-fluid d-flex justify-content-around align-items-center">
        <!-- Home | Misiones | Peleas | Chat -->
        <a href="{{ route('home') }}" class="text-white fs-4" title="Home">
            <i class="bi bi-house"></i>
        </a>
        <a href="{{ route('game.misiones') }}" class="text-white fs-4" title="Misiones">
            <i class="bi bi-map"></i>
        </a>
        <a href="{{ route('game.peleas') }}" class="text-white fs-4" title="Peleas">
            <i class="bi bi-lightning"></i>
        </a>
        <a href="{{ route('game.chat') }}" class="text-white fs-4" title="Chat">
            <i class="bi bi-chat-dots"></i>
        </a>
    </div>
</nav>
