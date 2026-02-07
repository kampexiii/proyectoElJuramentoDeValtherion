@extends('layouts.game.app')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Batalla contra el boss</h1>
            <div class="small text-secondary">{{ $mission->title ?? 'Mision' }} · Turno {{ $battle->turn_number }}</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('game.missions.run', $run) }}">Volver al run</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-secondary">Jugador</div>
                            <div class="fw-semibold">{{ $run->character?->name ?? 'Tu personaje' }}</div>
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
                            <div class="small text-secondary">Boss final</div>
                            <div class="fw-semibold">{{ $mission->finalBoss?->name ?? 'Boss' }}</div>
                        </div>
                        <i class="bi bi-fire fs-2 text-danger"></i>
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
        <div class="alert alert-info mt-3">
            Batalla finalizada. Resultado: {{ $battle->result ?? 'n/a' }}
        </div>
    @else
        <div class="card mt-3">
            <div class="card-body">
                <div class="mb-2 fw-semibold">Elige tu accion</div>
                <form method="POST" action="{{ route('game.missions.boss.fight', $run) }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <button type="submit" name="action" value="attack" class="btn btn-danger">
                        Atacar
                    </button>
                    <button type="submit" name="action" value="defend" class="btn btn-secondary">
                        Defender
                    </button>
                    <button type="submit" name="action" value="magic" class="btn btn-primary">
                        Magia
                    </button>
                </form>
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
