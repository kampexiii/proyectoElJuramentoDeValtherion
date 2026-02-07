@extends('layouts.game.app')
@section('body-class', 'screen-shell view-pvp layout-shell')
@section('main-class', 'layout-main')
@section('content')
<div class="view-pvp w-100 h-100 d-flex align-items-center justify-content-center">
    <section class="pvp-panel card shadow-sm w-100 p-3 p-md-4">
        <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1 class="h3 mb-1">Lobby PVP</h1>
                    <p class="small text-secondary mb-0">Crea una sala publica o unete a un rival disponible.</p>
                </div>
                <form method="POST" action="{{ route('pvp.rooms.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" @if($activeRoom) disabled @endif>
                        Crear sala
                    </button>
                </form>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($activeRoom)
                <div class="alert alert-warning d-flex align-items-center justify-content-between">
                    <div>
                        Ya estas en una sala activa. Debes cerrarla para crear o unirte a otra.
                    </div>
                    <a class="btn btn-sm btn-outline-dark" href="{{ route('pvp.rooms.show', $activeRoom) }}">
                        Ir a mi sala
                    </a>
                </div>
            @endif

            <div class="card">
                <div class="card-header">Salas abiertas</div>
                <div class="card-body">
                    @if($rooms->isEmpty())
                        <p class="text-secondary mb-0">No hay salas disponibles por ahora.</p>
                    @else
                        <div class="list-group">
                            @foreach($rooms as $room)
                                <div class="list-group-item d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div class="fw-semibold">Sala de {{ $room->owner->name ?? 'Jugador' }}</div>
                                        <div class="small text-secondary">Creada {{ $room->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if($room->owner_user_id === auth()->id())
                                        <a class="btn btn-outline-secondary" href="{{ route('pvp.rooms.show', $room) }}">
                                            Ver sala
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('pvp.rooms.join', $room) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success" @if($activeRoom) disabled @endif>
                                                Unirse
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
