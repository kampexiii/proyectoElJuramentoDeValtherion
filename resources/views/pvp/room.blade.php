@extends('layouts.game.app')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Sala PVP</h1>
            <p class="small text-secondary mb-0">Owner: {{ $room->owner->name ?? 'Jugador' }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('pvp.lobby') }}">Volver al lobby</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between">
                <div>
                    <div class="small text-secondary">Estado</div>
                    <div class="fw-semibold text-uppercase">{{ $room->status->value }}</div>
                </div>
                <div class="mt-3 mt-md-0">
                    <div class="small text-secondary">Rival</div>
                    <div class="fw-semibold">{{ $room->guest->name ?? 'Esperando...' }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($room->status->value === 'open' && !$room->guest_user_id)
        <div class="alert alert-info">Esperando rival...</div>
    @endif

    @if($room->status->value === 'in_progress')
        <a class="btn btn-primary" href="{{ route('pvp.rooms.battle', $room) }}">
            Ir a batalla
        </a>
    @endif

    @if($room->status->value === 'finished')
        <form method="POST" action="{{ route('pvp.rooms.close', $room) }}">
            @csrf
            <button type="submit" class="btn btn-danger">Cerrar sala</button>
        </form>
    @endif

    @if($room->status->value === 'open' && !$room->guest_user_id && $room->owner_user_id === auth()->id())
        <form method="POST" action="{{ route('pvp.rooms.close', $room) }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Cancelar sala</button>
        </form>
    @endif
</div>
@endsection
