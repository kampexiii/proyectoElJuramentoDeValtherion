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

        <!-- Crónica Mensual: clasificación mes anterior -->
        <div class="col-12 col-lg-6 d-flex flex-column" style="min-height: 0;">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm overflow-hidden" style="background-color: #111;">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Crónica Mensual (mes anterior)</div>
                <div class="card-body p-3 overflow-auto">
                    <div class="text-center mx-auto w-75 mb-3">
                        <h5 class="mb-1">Crónica Mensual</h5>
                        <p class="small mb-0 text-secondary">Relato breve de las gestas del mes pasado, una tabla con las razas mejor valoradas y la posición provisional si no hay datos.</p>
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
                                        <th style="width:80px">Puesto</th>
                                        <th>Raza</th>
                                        <th style="width:110px">Puntos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seasonRankings as $i => $r)
                                        @php
                                            $pos = ($r->place ?? ($i + 1));
                                            $name = $r->race_name ?? ('Raza #' . ($r->race_id ?? $pos));
                                            $points = $r->points ?? 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if($pos == 1)
                                                    <span class="badge bg-warning text-dark">{{ $pos }} 👑</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $pos }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $name }}</td>
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
@endsection
