@extends('layouts.game.app')

@section('content')
@php
    $character = $run->character;
    $playerSprite = $character?->sprite_url ?? '/assets/characters/placeholder.png';
    $playerSpriteUrl = str_starts_with($playerSprite, 'http') || str_starts_with($playerSprite, '//')
        ? $playerSprite
        : asset(ltrim($playerSprite, '/'));
    $playerPlaceholderUrl = asset('assets/characters/placeholder.svg');
    $boss = $mission->finalBoss;
    $bossSprite = $boss?->sprite_path ?: '/assets/sprites/bosses/placeholder.svg';
    $bossSpriteUrl = asset(ltrim($bossSprite, '/'));
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
@endphp
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
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

    <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
        <div class="card-body">
            @if ($battleFinished)
                <div class="alert alert-{{ $battle->result === 'p1_win' ? 'success' : 'danger' }} text-center fw-bold">
                    {{ $resultLabel }}
                </div>
            @endif
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="text-uppercase small text-secondary">Jugador</div>
                    <div class="fw-semibold">{{ $run->character?->name ?? 'Tu personaje' }}</div>
                    <div class="small text-secondary">HP: {{ $playerHpMax > 0 ? $playerHp . ' / ' . $playerHpMax : '-- / --' }}</div>
                    <div class="rounded bg-secondary" style="height: 8px;">
                        <div class="rounded bg-success" style="height: 8px; width: {{ $playerHpPercent }}%;"></div>
                    </div>
                    <div class="mt-3 rounded bg-dark d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img
                            src="{{ $playerSpriteUrl }}"
                            alt="Sprite del jugador"
                            style="height: clamp(140px, 22vw, 220px); width: 100%; object-fit: contain;"
                            onerror="this.onerror=null;this.src='{{ $playerPlaceholderUrl }}';"
                        >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-uppercase small text-secondary">Boss</div>
                    <div class="fw-semibold">{{ $mission->finalBoss?->name ?? 'Boss' }}</div>
                    <div class="small text-secondary">HP: {{ $bossHpMax > 0 ? $bossHp . ' / ' . $bossHpMax : '-- / --' }}</div>
                    <div class="rounded bg-danger" style="height: 8px;">
                        <div class="rounded bg-danger" style="height: 8px; width: {{ $bossHpPercent }}%;"></div>
                    </div>
                    <div class="mt-3 rounded bg-dark d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img
                            src="{{ $bossSpriteUrl }}"
                            alt="Sprite del boss"
                            style="height: clamp(140px, 22vw, 220px); width: 100%; object-fit: contain; transform: scaleX(-1);"
                            onerror="this.onerror=null;this.src='{{ $bossPlaceholderUrl }}';"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="fw-semibold">Acciones</div>
                <div class="small text-secondary">Pociones: {{ $potionsLeft }}</div>
            </div>
            <form method="POST" action="{{ route('game.missions.boss.action', $run) }}" class="d-flex flex-wrap gap-2">
                @csrf
                <button type="submit" name="action" value="attack" class="btn btn-outline-danger" @disabled($battleFinished)>Atacar</button>
                <button type="submit" name="action" value="magic" class="btn btn-outline-info" @disabled($battleFinished)>Magia</button>
                <button type="submit" name="action" value="defend" class="btn btn-outline-light" @disabled($battleFinished)>Defender</button>
                <button type="submit" name="action" value="potion" class="btn btn-outline-success" @disabled($battleFinished || $potionsLeft <= 0)>Pocion</button>
            </form>
            <div class="small text-secondary mt-2">Acciones deterministas sin RNG.</div>
        </div>
    </div>

    <div class="card mt-3">
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
    </div>
</div>
@endsection
