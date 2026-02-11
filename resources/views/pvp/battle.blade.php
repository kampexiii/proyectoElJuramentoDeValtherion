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
    $lastTurn = $turns->last();
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
    <div
        id="turn-timer"
        class="battle-timer"
        style="position: fixed; top: 1rem; right: 1rem; z-index: 2000; padding: 0.4rem 0.75rem; border-radius: 999px; background: rgba(15, 23, 42, 0.9); color: #fff; font-weight: 600;"
    >
        ⏳ 60s
    </div>
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

    <div class="battle-turn-log mt-3">
        @if(!$lastTurn)
            <div class="text-secondary">Aun no hay turnos registrados.</div>
        @else
            @php
                $notes = $lastTurn->notes_json ?? [];
                $lines = $notes['lines'] ?? [];
                $summary = $notes['summary'] ?? ('Turno ' . $lastTurn->turn_number);
                $detailText = !empty($lines)
                    ? implode(' | ', array_map('strip_tags', $lines))
                    : $summary;
            @endphp
            <div class="battle-turn-row fw-semibold">Turno {{ $lastTurn->turn_number }}</div>
            <div class="battle-turn-row small text-secondary">{{ $detailText }}</div>
        @endif
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
            </div>
        @endif
    </div>
</div>

<script>
(() => {
    const stateUrl = "{{ route('battle-rooms.state', $room) }}";
    const autoResolveUrl = "{{ route('battle-rooms.auto-resolve', $room) }}";
    const timerEl = document.getElementById('turn-timer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let stateVersion = null;
    let turnNumber = null;
    let serverUnix = null;
    let deadlineUnix = null;
    let lastSyncMs = Date.now();
    let autoResolveTriggered = false;

    const updateTimer = () => {
        if (!timerEl || serverUnix === null || deadlineUnix === null) {
            return;
        }
        const elapsed = (Date.now() - lastSyncMs) / 1000;
        const remaining = Math.max(0, Math.ceil(deadlineUnix - (serverUnix + elapsed)));
        timerEl.textContent = `⏳ ${remaining}s`;

        if (remaining <= 0 && !autoResolveTriggered) {
            autoResolveTriggered = true;
            fetch(autoResolveUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }).finally(() => {
                window.location.reload();
            });
        }
    };

    const applyState = (data) => {
        if (!data) {
            return;
        }

        if (stateVersion !== null && data.state_version !== stateVersion) {
            window.location.reload();
            return;
        }

        if (data.room_status && data.room_status !== 'active') {
            window.location.reload();
            return;
        }

        if (turnNumber !== null && data.turn_number !== turnNumber) {
            autoResolveTriggered = false;
        }

        stateVersion = data.state_version;
        turnNumber = data.turn_number;
        serverUnix = data.server_unix;
        deadlineUnix = data.turn_deadline_unix;
        lastSyncMs = Date.now();
    };

    const poll = () => {
        fetch(stateUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then((response) => (response.ok ? response.json() : null))
            .then((data) => applyState(data))
            .catch(() => {});
    };

    poll();
    updateTimer();
    setInterval(poll, 2000);
    setInterval(updateTimer, 1000);
})();
</script>

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
