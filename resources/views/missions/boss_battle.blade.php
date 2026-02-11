@extends('layouts.game.app')

@section('body-class', 'screen-shell view-battle layout-shell')
@section('main-class', 'layout-main')
@section('content')
@php
    $character = $run->character;
    $playerSprite = $character?->sprite_url ?? '/assets/characters/placeholder.svg';
    $playerSpriteUrl = str_starts_with($playerSprite, 'http') || str_starts_with($playerSprite, '//')
        ? $playerSprite
        : asset(ltrim($playerSprite, '/'));
    $playerPlaceholderUrl = asset('assets/characters/placeholder.svg');
    $boss = $mission->finalBoss;
    $bossSprite = $boss?->sprite_path ?: '/assets/sprites/bosses/placeholder.svg';
    $bossSpritePath = ltrim($bossSprite, '/');
    $bossSpriteUrl = asset($bossSpritePath);
    $bossSpriteDiskPath = public_path($bossSpritePath);
    if (!file_exists($bossSpriteDiskPath)) {
        $altSpritePath = null;
        if (str_ends_with($bossSpritePath, '.png.png')) {
            $altSpritePath = substr($bossSpritePath, 0, -4);
        } elseif (str_ends_with($bossSpritePath, '.png')) {
            $altSpritePath = $bossSpritePath . '.png';
        }

        if ($altSpritePath && file_exists(public_path($altSpritePath))) {
            $bossSpriteUrl = asset($altSpritePath);
        }
    }
    $bossPlaceholderUrl = asset('assets/bosses/placeholder.svg');

    $p1Stats = is_array($battle->stats_p1_json ?? null) ? $battle->stats_p1_json : [];
    $p2Stats = is_array($battle->stats_p2_json ?? null) ? $battle->stats_p2_json : [];
    $playerHpMax = (int) ($p1Stats['hp_max'] ?? 0);
    $bossHpMax = (int) ($p2Stats['hp_max'] ?? 0);
    $playerHp = (int) ($battle->p1_hp ?? 0);
    $bossHp = (int) ($battle->p2_hp ?? 0);
    $playerHpPercent = $playerHpMax > 0 ? min(100, (int) floor(($playerHp / $playerHpMax) * 100)) : 0;
    $bossHpPercent = $bossHpMax > 0 ? min(100, (int) floor(($bossHp / $bossHpMax) * 100)) : 0;
    $potionsLeft = (int) ($battle->p1_potions_left ?? 0);
    $battleFinished = $battle->status === \App\Enums\BattleStatus::Finished;
    $resultLabel = $battle->result === 'p1_win' ? 'VICTORIA' : ($battle->result === 'p2_win' ? 'DERROTA' : 'FIN');
    $resultText = $battle->result === 'p1_win' ? 'Has ganado' : ($battle->result === 'p2_win' ? 'Has perdido' : 'Empate');
@endphp
<div class="view-battle w-100 h-100 d-flex align-items-center justify-content-center">
    <section class="battle-arena w-100">
        <div class="battle-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1 class="h3 mb-1">Batalla contra el boss</h1>
                    <div class="small text-secondary">{{ $mission->title ?? 'Mision' }} · {{ $mission->finalBoss?->name ?? 'Boss' }}</div>
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
        </div>

        <div class="battle-body">
            <div class="battle-main">
                <div class="battle-stage">
                    <div class="battle-side">
                        <div class="hud">
                            <div class="text-uppercase small text-secondary">Jugador</div>
                            <div class="fw-semibold">{{ $run->character?->name ?? 'Tu personaje' }}</div>
                            <div class="small text-secondary">HP: {{ $playerHpMax > 0 ? $playerHp . ' / ' . $playerHpMax : '-- / --' }}</div>
                            <div class="battle-hp-bar">
                                <div class="battle-hp-bar-fill" style="width: {{ $playerHpPercent }}%;"></div>
                            </div>
                        </div>
                        <div class="sprite-slot">
                            <img
                                src="{{ $playerSpriteUrl }}"
                                alt="Sprite del jugador"
                                class="sprite"
                                onerror="this.onerror=null;this.src='{{ $playerPlaceholderUrl }}';"
                            >
                        </div>
                    </div>
                    <div class="battle-side battle-side-right">
                        <div class="hud">
                            <div class="text-uppercase small text-secondary">Boss</div>
                            <div class="fw-semibold">{{ $mission->finalBoss?->name ?? 'Boss' }}</div>
                            <div class="small text-secondary">HP: {{ $bossHpMax > 0 ? $bossHp . ' / ' . $bossHpMax : '-- / --' }}</div>
                            <div class="battle-hp-bar">
                                <div class="battle-hp-bar-fill is-danger" style="width: {{ $bossHpPercent }}%;"></div>
                            </div>
                        </div>
                        <div class="sprite-slot">
                            <img
                                src="{{ $bossSpriteUrl }}"
                                alt="Sprite del boss"
                                class="sprite sprite-boss"
                                onerror="this.onerror=null;this.src='{{ $bossPlaceholderUrl }}';"
                            >
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        @if ($battleFinished)
                            <div class="alert alert-{{ $battle->result === 'p1_win' ? 'success' : 'danger' }} text-center fw-bold">
                                {{ $resultLabel }}
                            </div>
                        @endif
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div class="fw-semibold">Acciones</div>
                            <div class="small text-secondary">Pociones: {{ $potionsLeft }}</div>
                        </div>
                        <form method="POST" action="{{ route('game.missions.boss.action', $run) }}" class="battle-actions">
                            @csrf
                            <button type="submit" name="action" value="attack" class="btn btn-outline-danger" @disabled($battleFinished)>Atacar</button>
                            <button type="submit" name="action" value="magic" class="btn btn-outline-info" @disabled($battleFinished)>Magia</button>
                            <button type="submit" name="action" value="defend" class="btn btn-outline-light" @disabled($battleFinished)>Defender</button>
                            <button type="submit" name="action" value="potion" class="btn btn-outline-success" @disabled($battleFinished || $potionsLeft <= 0)>Pocion</button>
                        </form>
                        <div class="small text-secondary mt-2">Acciones deterministas sin RNG.</div>
                    </div>
                </div>
            </div>

            <aside class="battle-aside card">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Registro de turnos</div>
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
            </aside>
        </div>
    </section>
</div>

@if ($battleFinished)
    <div class="battle-overlay" role="dialog" aria-modal="true">
        <div class="battle-overlay-card">
            <div class="battle-overlay-title">Fin del duelo</div>
            <div class="battle-overlay-subtitle">{{ $resultText }}</div>
            <a href="{{ route('game.missions.run', $run) }}" class="btn btn-light w-100">Salir</a>
        </div>
    </div>
@endif
@endsection
