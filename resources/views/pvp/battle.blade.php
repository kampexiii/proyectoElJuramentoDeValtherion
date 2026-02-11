@extends('layouts.game.app')
@section('body-class', 'screen-shell view-battle-pvp layout-shell')
@section('content')
@php
    $bgClass = 'battle-bg-plains';
    $p1MaxRaw = (int) ($battle->stats_p1_json['hp'] ?? 0);
    $p2MaxRaw = (int) ($battle->stats_p2_json['hp'] ?? 0);
    $p1Max = $p1MaxRaw > 0 ? $p1MaxRaw : null;
    $p2Max = $p2MaxRaw > 0 ? $p2MaxRaw : null;
    $p1Hp = max(0, (int) $battle->p1_hp);
    $p2Hp = max(0, (int) $battle->p2_hp);
    $p1Pct = $p1Max ? min(100, (int) floor(($p1Hp / $p1Max) * 100)) : 0;
    $p2Pct = $p2Max ? min(100, (int) floor(($p2Hp / $p2Max) * 100)) : 0;
    $recentTurns = $turns->count() > 5 ? $turns->slice(-5) : $turns;
    $roomClosed = $room->status === \App\Enums\BattleRoomStatus::Closed;
    $battleFinished = $battle->status === \App\Enums\BattleStatus::Finished;
    $placeholderSprite = asset('assets/characters/placeholder.svg');
    $p1SpriteRaw = $battle->player1Character?->sprite_url ?? '/assets/characters/placeholder.svg';
    $p2SpriteRaw = $battle->player2Character?->sprite_url ?? '/assets/characters/placeholder.svg';
    $p1SpriteUrl = str_starts_with($p1SpriteRaw, 'http') || str_starts_with($p1SpriteRaw, '//')
        ? $p1SpriteRaw
        : asset(ltrim($p1SpriteRaw, '/'));
    $p2SpriteUrl = str_starts_with($p2SpriteRaw, 'http') || str_starts_with($p2SpriteRaw, '//')
        ? $p2SpriteRaw
        : asset(ltrim($p2SpriteRaw, '/'));
    $resultText = 'Empate';
    if ($battle->result === 'p1_win') {
        $resultText = $playerSide === 'p1' ? 'Has ganado' : 'Has perdido';
    } elseif ($battle->result === 'p2_win') {
        $resultText = $playerSide === 'p2' ? 'Has ganado' : 'Has perdido';
    }
@endphp
<div class="container py-4 battle-shell">
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

    <div class="battle-stage {{ $bgClass }}">
        <div class="battle-stage-inner">
            <div class="battle-side battle-side-left">
                <div class="battle-hp">
                    <div class="battle-hp-label">{{ $room->owner->name ?? 'Jugador' }}</div>
                    <div class="battle-hp-text">
                        HP: {{ $p1Hp }}@if($p1Max) / {{ $p1Max }}@endif
                    </div>
                    <div class="battle-hp-bar">
                        <div class="battle-hp-bar-fill" style="width: {{ $p1Pct }}%"></div>
                    </div>
                </div>
                <img
                    src="{{ $p1SpriteUrl }}"
                    alt="Sprite del jugador"
                    class="battle-sprite-img"
                    onerror="this.onerror=null;this.src='{{ $placeholderSprite }}';"
                >
                <div class="battle-sprite-label">Jugador</div>
            </div>
            <div class="battle-side battle-side-right">
                <div class="battle-hp">
                    <div class="battle-hp-label">{{ $room->guest->name ?? 'Rival' }}</div>
                    <div class="battle-hp-text">
                        HP: {{ $p2Hp }}@if($p2Max) / {{ $p2Max }}@endif
                    </div>
                    <div class="battle-hp-bar">
                        <div class="battle-hp-bar-fill battle-hp-bar-fill-danger" style="width: {{ $p2Pct }}%"></div>
                    </div>
                </div>
                <img
                    src="{{ $p2SpriteUrl }}"
                    alt="Sprite del rival"
                    class="battle-sprite-img battle-sprite-img-rival"
                    onerror="this.onerror=null;this.src='{{ $placeholderSprite }}';"
                >
                <div class="battle-sprite-label">Rival</div>
            </div>
        </div>
    </div>

    <div class="battle-panel mt-3">
        @if(!$battleFinished && !$roomClosed)
            <div class="battle-panel-header">
                <div class="fw-semibold">Elige tu accion</div>
                @if($myPending && !$theirPending)
                    <div class="small text-secondary">Esperando rival...</div>
                @elseif($myPending && $theirPending)
                    <div class="small text-secondary">Resolviendo turno...</div>
                @endif
            </div>
            <div class="battle-panel-actions">
                <form method="POST" action="{{ route('pvp.rooms.battle.submit', $room) }}" class="battle-action-grid">
                    @csrf
                    <button type="submit" name="action" value="attack" class="battle-action-btn battle-action-btn-attack" @if($myPending) disabled @endif>
                        Atacar
                    </button>
                    <button type="submit" name="action" value="magic" class="battle-action-btn battle-action-btn-magic" @if($myPending) disabled @endif>
                        Magia
                    </button>
                    <button type="submit" name="action" value="defend" class="battle-action-btn battle-action-btn-defend" @if($myPending) disabled @endif>
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

@if($battleFinished || $roomClosed)
    <div class="battle-overlay" role="dialog" aria-modal="true">
        <div class="battle-overlay-card">
            @if($roomClosed)
                <div class="battle-overlay-title">Sala cerrada por el anfitrion</div>
                <div class="battle-overlay-subtitle">Vuelve al lobby para unirte a otra sala.</div>
                <a href="{{ route('pvp.lobby') }}" class="btn btn-light w-100">Volver</a>
            @else
                <div class="battle-overlay-title">Fin del duelo</div>
                <div class="battle-overlay-subtitle">{{ $resultText }}</div>
                @if($room->owner_user_id === auth()->id())
                    <form method="POST" action="{{ route('pvp.rooms.close', $room) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">Salir y cerrar sala</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('pvp.rooms.leave', $room) }}">
                        @csrf
                        <button type="submit" class="btn btn-light w-100">Salir</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
@endif
@endsection
