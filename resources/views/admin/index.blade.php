@extends('layouts.game.app')

@section('content')
<div class="home-viewport">
    <div class="home-content flex-grow-1">
        <div class="row g-0 flex-grow-1 h-100">
            <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
                <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden hx-card-bg">
                    <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Panel de Administración</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-3 overflow-hidden">
                        <p class="mb-2">Acceso exclusivo del Alto Consejo.</p>
                        <p class="small text-secondary mb-0">Aquí añadiremos herramientas y controles del reino.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
                <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden hx-card-bg">
                    <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Estado del Bastión</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-3 overflow-hidden">
                        <p class="mb-2">Sesión de administrador verificada.</p>
                        <p class="small text-secondary mb-0">Panel preparado para futuras secciones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
