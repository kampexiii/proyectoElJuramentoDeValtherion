@extends('layouts.game.app')

@section('content')
<style>
    @media (max-width: 767.98px) {
        .home-viewport {
            height: 100vh;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .home-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
        }
        .home-panel {
            height: 50%;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }
        .home-panel .card-body {
            padding: 0.5rem !important;
            font-size: 0.95rem;
        }
        .home-panel .table {
            font-size: 0.95rem;
        }
        .home-panel .mx-auto.w-75 {
            width: 100% !important;
            max-width: 100% !important;
        }
        .home-panel .mb-3 {
            margin-bottom: 0.5rem !important;
        }
        .home-panel .card-header {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
            font-size: 1rem;
        }
        .home-panel .table-responsive {
            max-height: 100%;
        }
        .home-panel .table td, .home-panel .table th {
            padding: 0.3rem 0.5rem !important;
        }
    }
</style>
<div class="home-viewport">
    <div class="home-content flex-grow-1">
        <div class="row g-0 flex-grow-1 h-100">
        <!-- Bloque Personaje actual -->
        <div class="col-12 col-lg-6 d-flex flex-column home-panel" style="min-height: 0;">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Estado del Héroe</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-2 overflow-hidden">
                    <p class="mb-2 text-truncate w-100">Aún no has forjado tu leyenda.</p>
                    <a href="{{ route('game.perfil') }}" class="btn btn-primary btn-sm">Crear Personaje</a>
                </div>
            </div>
        </div>

        <!-- Crónica Mensual: clasificación mes anterior -->
        <div class="col-12 col-lg-6 d-flex flex-column home-panel" style="min-height: 0;">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm overflow-hidden" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Crónica Mensual (mes anterior)</div>
                <div class="card-body p-3 overflow-auto">
                    <div class="text-center mx-auto w-75 mb-3">
                        <h5 class="mb-1">Crónica Mensual</h5>
                        <p class="small mb-0 text-secondary d-none d-md-block">Relato breve de las gestas del mes pasado, una tabla con las razas mejor valoradas y la posición provisional si no hay datos.</p>
                    </div>

                    {{-- Bloque ganador del mes anterior --}}
                    <div class="mb-3">
                        @if (isset($seasonWinner) && $seasonWinner && !empty($seasonWinner->race_name))
                            <div class="p-2 px-3 rounded-4 bg-dark bg-opacity-75 border mb-1 text-center">
                                <span class="fw-bold text-warning">Ganador del mes anterior:</span>
                                <span class="ms-2 text-white">{{ $seasonWinner->race_name }}</span>
                            </div>
                        @else
                            <div class="p-2 px-3 rounded-4 bg-dark bg-opacity-75 border mb-1 text-center">
                                <span class="fw-bold text-warning">Ganador del mes anterior:</span>
                                <span class="ms-2 text-white">Aldrik Vhar <span class="text-secondary">(provisional)</span></span>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-secondary">Aún no hay cierres mensuales registrados.</small>
                            </div>
                        @endif
                    </div>

                    @if (!empty($fallbackMessage))
                        <div class="alert alert-secondary small mb-2">{{ $fallbackMessage }}</div>
                    @endif

                    @if ($seasonRankings && $seasonRankings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-dark mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:60px">Puesto</th>
                                        <th>Raza</th>
                                        <th style="width:70px">Puntos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seasonRankings as $i => $r)
                                        @php
                                            $pos = ($r->place ?? ($i + 1));
                                            $name = $r->race_name ?? ('Raza #' . ($r->race_id ?? $pos));
                                            $points = $r->points ?? 0;
                                        @endphp
                                        <tr @if($loop->index >= 6) class="d-none d-md-table-row" @endif>
                                            <td>
                                                @if($pos == 1)
                                                    <span class="badge bg-warning text-dark">{{ $pos }} 👑</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $pos }}</span>
                                                @endif
                                            </td>
                                            <td class="text-truncate" style="max-width:110px">{{ $name }}</td>
                                            <td><span class="badge bg-success">{{ $points }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(!empty($fallbackUsed) && $fallbackUsed === 'A')
                            <p class="small text-secondary mt-2">Clasificación provisional (sin puntos aún)</p>
                        @endif
                    @else
                        <p class="mb-0">Aún no hay rankings para ese mes.</p>
                    @endif
                </div>
            </div>
        </div>
</div>
</div>
</div>
@endsection
