@extends('layouts.game.app')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Batalla PVP</h1>
            <div class="small text-secondary">Turno {{ $battle->turn_number }}</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('pvp.rooms.show', $room) }}">Volver a la sala</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-secondary">Jugador 1</div>
                            <div class="fw-semibold">{{ $room->owner->name ?? 'Jugador' }}</div>
                        </div>
                        <i class="bi bi-person-badge fs-2 text-primary"></i>
                    </div>
                    @php
                        $p1Max = max(1, (int) ($battle->stats_p1_json['hp'] ?? 1));
                        $p1Hp = max(0, (int) $battle->p1_hp);
                        $p1Pct = min(100, (int) floor(($p1Hp / $p1Max) * 100));
                    @endphp
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small">
                            <span>HP</span>
                            <span>{{ $p1Hp }} / {{ $p1Max }}</span>
                        </div>
                        <div class="progress" role="progressbar" aria-valuenow="{{ $p1Pct }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" style="width: {{ $p1Pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-secondary">Jugador 2</div>
                            <div class="fw-semibold">{{ $room->guest->name ?? 'Rival' }}</div>
                        </div>
                        <i class="bi bi-person-badge fs-2 text-danger"></i>
                    </div>
                    @php
                        $p2Max = max(1, (int) ($battle->stats_p2_json['hp'] ?? 1));
                        $p2Hp = max(0, (int) $battle->p2_hp);
                        $p2Pct = min(100, (int) floor(($p2Hp / $p2Max) * 100));
                    @endphp
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small">
                            <span>HP</span>
                            <span>{{ $p2Hp }} / {{ $p2Max }}</span>
                        </div>
                        <div class="progress" role="progressbar" aria-valuenow="{{ $p2Pct }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-danger" style="width: {{ $p2Pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($battle->status->value === 'finished')
        <div class="alert alert-info mt-3">Batalla finalizada. Resultado: {{ $battle->result ?? 'n/a' }}</div>
    @else
        <div class="card mt-3">
            <div class="card-body">
                <div class="mb-2 fw-semibold">Elige tu accion</div>
                <form method="POST" action="{{ route('pvp.rooms.battle.submit', $room) }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <button type="submit" name="action" value="attack" class="btn btn-danger" @if($myPending) disabled @endif>
                        Atacar
                    </button>
                    <button type="submit" name="action" value="defend" class="btn btn-secondary" @if($myPending) disabled @endif>
                        Defender
                    </button>
                    <button type="submit" name="action" value="magic" class="btn btn-primary" @if($myPending) disabled @endif>
                        Magia
                    </button>
                </form>
                @if($myPending && !$theirPending)
                    <div class="small text-secondary mt-2">Esperando rival...</div>
                @elseif($myPending && $theirPending)
                    <div class="small text-secondary mt-2">Resolviendo turno...</div>
                @endif
            </div>
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-header">Registro de turnos</div>
        <div class="card-body">
            @if($turns->isEmpty())
                <div class="text-secondary">Aun no hay turnos registrados.</div>
            @else
                <div class="list-group">
                    @foreach($turns as $turn)
                        @php
                            $notes = $turn->notes_json ?? [];
                            $lines = $notes['lines'] ?? [];
                        @endphp
                        <div class="list-group-item">
                            <div class="fw-semibold">Turno {{ $turn->turn_number }}</div>
                            @if(!empty($lines))
                                <div class="small text-secondary">{!! implode('<br>', array_map('e', $lines)) !!}</div>
                            @else
                                <div class="small text-secondary">{{ $notes['summary'] ?? 'Sin detalles.' }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
