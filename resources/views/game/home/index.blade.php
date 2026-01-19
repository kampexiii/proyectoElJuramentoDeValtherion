@extends('layouts.game.app')

@section('content')
<div class="h-100 d-flex flex-column">
    <div class="row g-3 flex-grow-1">
        <!-- Bloque Personaje actual -->
        <div class="col-12 col-md-6">
            <div class="card bg-zinc-900 border-secondary h-100 text-white shadow-sm" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center">Estado del Héroe</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <p class="mb-2">Aún no has forjado tu leyenda.</p>
                    <a href="{{ route('game.perfil') }}" class="btn btn-primary">Crear Personaje</a>
                </div>
            </div>
        </div>

        <!-- Bloque Resumen rápido -->
        <div class="col-12 col-md-6">
            <div class="card bg-zinc-900 border-secondary h-100 text-white shadow-sm" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center">Información de Cuenta</div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <ul class="list-unstyled mb-0 text-center">
                        <li class="mb-2"><strong>Usuario:</strong> {{ auth()->user()->name }}</li>
                        <li class="mb-2"><strong>Plan:</strong> {{ strtoupper(auth()->user()->plan ?? 'free') }}</li>
                        <li><strong>Rol:</strong> {{ strtoupper(auth()->user()->role ?? 'user') }}</li>
                    </ul>
                    <hr class="border-secondary">
                    <p class="small text-secondary mb-0 text-center">Crea un personaje para empezar a acumular oro y experiencia.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
