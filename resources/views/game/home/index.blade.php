@extends('layouts.game.app')

@section('content')
<div class="h-100 d-flex flex-column">
    <div class="row g-3 flex-grow-1">
        <!-- Bloque Personaje actual -->
        <div class="col-12 col-md-6">
            <div class="card bg-zinc-900 border-secondary h-100 text-white shadow-sm" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark">Personaje Actual</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <p class="mb-0">Aquí se verá el sprite del personaje actual</p>
                    <small class="text-secondary">(Placeholder del personaje)</small>
                </div>
            </div>
        </div>

        <!-- Bloque Resumen rápido -->
        <div class="col-12 col-md-6">
            <div class="card bg-zinc-900 border-secondary h-100 text-white shadow-sm" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark">Resumen Rápido</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Oro:</strong> 100 <i class="bi bi-coin text-warning"></i></li>
                        <li><strong>Nivel:</strong> 10 <i class="bi bi-arrow-up-circle text-success"></i></li>
                        <li><strong>EXP:</strong> 450/1000</li>
                        <li><strong>Montura:</strong> No <i class="bi bi-x-circle text-danger"></i></li>
                        <li><strong>Plan:</strong> {{ strtoupper(auth()->user()->plan ?? 'free') }}</li>
                    </ul>
                    <hr class="border-secondary">
                    <p class="small text-secondary mb-0">Aquí irá el resumen de estadísticas y recursos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bloque Acciones rápidas -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark">Acciones Rápidas</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4">
                            <a href="{{ route('game.misiones') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-map d-block fs-4 mb-1"></i>
                                Misiones
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('game.peleas') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="bi bi-lightning d-block fs-4 mb-1"></i>
                                Peleas
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('game.inventario') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-backpack d-block fs-4 mb-1"></i>
                                Inventario
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
