@extends('layouts.game.app')

@section('content')
@php
    $character = $run->character;
    $playerSprite = $character?->sprite_url ?? '/assets/characters/placeholder.png';
    $playerSpriteUrl = asset(ltrim($playerSprite, '/'));
    $playerPlaceholderUrl = asset('assets/characters/placeholder.png');
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
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="text-uppercase small text-secondary">Jugador</div>
                    <div class="fw-semibold">{{ $run->character?->name ?? 'Tu personaje' }}</div>
                    <div class="small text-secondary">HP: -- / --</div>
                    <div class="rounded bg-secondary" style="height: 8px;"></div>
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
                    <div class="small text-secondary">HP: -- / --</div>
                    <div class="rounded bg-danger" style="height: 8px;"></div>
                    <div class="mt-3 rounded bg-dark" style="height: 180px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="fw-semibold mb-2">Acciones</div>
            <form method="POST" action="{{ route('game.missions.boss.action', $run) }}" class="d-flex flex-wrap gap-2">
                @csrf
                <button type="submit" name="action" value="attack" class="btn btn-outline-danger">Atacar</button>
                <button type="submit" name="action" value="magic" class="btn btn-outline-info">Magia</button>
                <button type="submit" name="action" value="defend" class="btn btn-outline-light">Defender</button>
            </form>
            <div class="small text-secondary mt-2">Motor en construccion: la accion solo se registra.</div>
        </div>
    </div>
</div>
@endsection
