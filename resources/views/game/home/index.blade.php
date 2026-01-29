@extends('layouts.game.app')

@section('content')
<div class="home-viewport">
    <div class="home-content flex-grow-1">
        <div class="row g-0 flex-grow-1 h-100">
        <!-- Bloque Personaje actual -->
        <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden hx-card-bg">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Estado del Héroe</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-2 overflow-hidden">
                    @if (empty($character))
                        <p class="mb-2 text-truncate w-100">Aún no has forjado tu leyenda.</p>
                        <a href="{{ route('game.personaje.create') }}" class="btn btn-primary btn-sm">Crear personaje</a>
                    @else
                        <p class="mb-2 text-truncate w-100">Nombre: {{ $character->name }}</p>
                        <p class="mb-2 text-truncate w-100">Raza: {{ $character->race->name ?? 'Pendiente' }}</p>
                        <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-light btn-sm">Editar personaje</a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Crónica Mensual: clasificación mes anterior -->
        <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm overflow-hidden hx-card-bg">
                <div class="card-body p-3 overflow-auto">
                    <div class="text-center mx-auto w-75 mb-3">
                        <h5 class="mb-1">{{ $chronicleTitle ?? 'Crónica Mensual' }}</h5>
                    </div>

                    {{-- Bloque ganador del mes anterior --}}
                    <div class="mb-3">
                        <div class="p-2 px-3 rounded-4 bg-dark bg-opacity-75 border mb-1 text-center">
                            <span class="fw-bold text-warning">{{ $winnerLabel ?? 'Ganador del mes:' }}</span>
                            <span class="ms-2 text-white">{{ $winnerName ?? '' }}</span>
                        </div>
                    </div>

                    @if (!empty($fallbackMessage))
                        <div class="alert alert-secondary small mb-2">{{ $fallbackMessage }}</div>
                    @endif

                    @if ($seasonRankings && $seasonRankings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-dark mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="hx-col-puesto">Puesto</th>
                                        <th>Raza</th>
                                        <th class="hx-col-puntos">Puntos</th>
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
                                            <td class="text-truncate hx-name-max">{{ $name }}</td>
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
