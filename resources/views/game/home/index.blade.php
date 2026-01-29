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

                    @if (!empty($fallbackMessage))
                        <div class="alert alert-warning small mb-2">{{ $fallbackMessage }}</div>
                    @endif

                    @if ($seasonWinner)
                        <p class="mb-2"><strong>Raza ganadora del mes:</strong> {{ $seasonWinner->race_name ?? ('Raza #' . ($seasonWinner->race_id ?? '—')) }}</p>
                    @endif

                    @if ($seasonRankings && $seasonRankings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-dark mb-0">
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
                                                    <span class="badge bg-warning text-dark">🥇 {{ $pos }}</span>
                                                @elseif($pos == 2)
                                                    <span class="badge bg-secondary">🥈 {{ $pos }}</span>
                                                @elseif($pos == 3)
                                                    <span class="badge bg-info">🥉 {{ $pos }}</span>
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
                            <p class="small text-muted mt-2">Clasificación provisional (orden por nombre, puntos 0).</p>
                        @endif
                    @else
                        <p class="mb-0">Aún no hay rankings para ese mes.</p>
                    @endif
                </div>
            </div>
        </div>
</div>
@endsection
