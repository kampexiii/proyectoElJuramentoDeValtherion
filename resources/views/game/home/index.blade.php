@extends('layouts.game.app')

@section('content')
<div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden">
    <div class="row g-3 flex-grow-1 overflow-hidden">
        <!-- Bloque Personaje actual -->
        <div class="col-12 col-lg-6 d-flex flex-column" style="min-height: 0;">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Estado del Héroe</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-2 overflow-hidden">
                    <p class="mb-2 text-truncate w-100">Aún no has forjado tu leyenda.</p>
                    <a href="{{ route('game.perfil') }}" class="btn btn-primary btn-sm">Crear Personaje</a>
                </div>
            </div>
        </div>

        <!-- Bloque Resumen rápido -->
        <div class="col-12 col-lg-6 d-flex flex-column" style="min-height: 0;">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Información de Cuenta</div>
                <div class="card-body d-flex flex-column justify-content-center p-2 overflow-hidden">
                    <ul class="list-unstyled mb-0 text-center small">
                        <li class="mb-1 text-truncate"><strong>Usuario:</strong> {{ auth()->user()->name }}</li>
                        <li class="mb-1 text-truncate"><strong>Plan:</strong> <span class="badge bg-secondary">{{ strtoupper(auth()->user()->plan ?? 'free') }}</span></li>
                        <li class="text-truncate"><strong>Rol:</strong> {{ strtoupper(auth()->user()->role ?? 'user') }}</li>
                    </ul>
                    <hr class="my-2 border-secondary opacity-25">
                    <p class="small text-secondary mb-0 text-center lh-sm">Crea un personaje para empezar a acumular oro y experiencia.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
