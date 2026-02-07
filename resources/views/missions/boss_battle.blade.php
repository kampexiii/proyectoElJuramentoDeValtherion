@extends('layouts.game.app')
@section('content')
@php
    $bgClass = 'battle-bg-ruins';
    $bossSprite = $mission->finalBoss?->sprite_url ?? '/assets/bosses/placeholder.png';
    $playerSprite = '/assets/characters/placeholder.png';
    $p1MaxRaw = (int) ($battle->stats_p1_json['hp'] ?? 0);
    $p2MaxRaw = (int) ($battle->stats_p2_json['hp'] ?? 0);
    $p1Max = $p1MaxRaw > 0 ? $p1MaxRaw : null;
    $p2Max = $p2MaxRaw > 0 ? $p2MaxRaw : null;
    $p1Hp = max(0, (int) $battle->p1_hp);
    $p2Hp = max(0, (int) $battle->p2_hp);
    $p1Pct = $p1Max ? min(100, (int) floor(($p1Hp / $p1Max) * 100)) : 0;
    $p2Pct = $p2Max ? min(100, (int) floor(($p2Hp / $p2Max) * 100)) : 0;
    $recentTurns = $turns->count() > 5 ? $turns->slice(-5) : $turns;
@endphp
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

    <div class="battle-stage {{ $bgClass }}">
        <div class="battle-stage-inner">
            <div class="battle-side battle-side-left">
                <div class="battle-hp">
                    <div class="battle-hp-label">{{ $run->character?->name ?? 'Tu personaje' }}</div>
                    <div class="battle-hp-text">
                        HP: {{ $p1Hp }}@if($p1Max) / {{ $p1Max }}@endif
                    </div>
                    <div class="battle-hp-bar">
                        <div class="battle-hp-bar-fill" style="width: {{ $p1Pct }}%"></div>
                    </div>
                </div>
                <div class="battle-sprite battle-sprite-player" style="background-image: url('{{ $playerSprite }}'); background-size: cover; background-position: center;" aria-hidden="true"></div>
                <div class="battle-sprite-label">Jugador</div>
            </div>
            <div class="battle-side battle-side-right">
                <div class="battle-hp">
                    <div class="battle-hp-label">{{ $mission->finalBoss?->name ?? 'Boss' }}</div>
                    <div class="battle-hp-text">
                        HP: {{ $p2Hp }}@if($p2Max) / {{ $p2Max }}@endif
                    </div>
                    <div class="battle-hp-bar">
                        <div class="battle-hp-bar-fill battle-hp-bar-fill-danger" style="width: {{ $p2Pct }}%"></div>
                    </div>
                </div>
                <div class="battle-sprite battle-sprite-boss" style="background-image: url('{{ $bossSprite }}'); background-size: cover; background-position: center;" aria-hidden="true"></div>
                <div class="battle-sprite-label">Boss</div>
            </div>
        </div>
    </div>

    <div class="battle-panel mt-3">
        @if($battle->status->value === 'finished')
            <div class="battle-panel-header">
                <div class="fw-semibold">Batalla finalizada</div>
                <div class="small text-secondary">Resultado: {{ $battle->result ?? 'n/a' }}</div>
            </div>
        @else
            <div class="battle-panel-header">
                <div class="fw-semibold">Elige tu accion</div>
            </div>
            <div class="battle-panel-actions">
                <form method="POST" action="{{ route('game.missions.boss.fight', $run) }}" class="battle-action-grid">
                    @csrf
                    <button type="submit" name="action" value="attack" class="battle-action-btn battle-action-btn-attack">
                        Atacar
                    </button>
                    <button type="submit" name="action" value="magic" class="battle-action-btn battle-action-btn-magic">
                        Magia
                    </button>
                    <button type="submit" name="action" value="defend" class="battle-action-btn battle-action-btn-defend">
                        Defender
                    </button>
                </form>
                <button type="button" class="battle-action-btn battle-action-btn-ghost" disabled>
                    Rendirse (proximamente)
                </button>
            </div>
        @endif
    </div>

    <div class="card mt-3">
        <div class="card-header">Registro de turnos</div>
        <div class="card-body">
            @if($recentTurns->isEmpty())
                <div class="text-secondary">Aun no hay turnos registrados.</div>
            @else
                <div class="list-group">
                    @foreach($recentTurns as $turn)
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
