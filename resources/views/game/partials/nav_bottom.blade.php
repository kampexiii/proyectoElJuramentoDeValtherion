<nav class="navbar navbar-game navbar-dark bg-dark border-top border-secondary">
    <div class="container-fluid d-flex justify-content-around align-items-center">
        <!-- Home | Misiones | PVP | Chat -->
        <a href="{{ route('home') }}" class="text-white fs-4" title="Home">
            <i class="bi bi-house"></i>
        </a>
        <a href="{{ route('game.missions.index') }}" class="text-white fs-4" title="Misiones">
            <i class="bi bi-map"></i>
        </a>
        <a href="{{ route('pvp.lobby') }}" class="text-white fs-4" title="PVP">
            <i class="bi bi-lightning"></i>
        </a>
        <a href="{{ route('game.chat') }}" class="text-white fs-4" title="Chat">
            <i class="bi bi-chat-dots"></i>
        </a>
    </div>
</nav>
