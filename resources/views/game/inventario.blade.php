@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-12 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h2 class="h5 mb-0">Inventario</h2>
                    <p class="small text-secondary mb-0">Tu equipo disponible para equipar.</p>
                </div>
                <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-light btn-sm">Equipar</a>
            </div>

            @if ($inventory->count() > 6)
                <p class="small text-secondary mt-2 mb-0">Ver más (próximamente).</p>
            @else
                <div class="row g-2">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="col-6 col-lg-4">
                            <div class="card bg-dark border-secondary h-100">
                                <div class="card-body p-2 d-flex flex-column">
                                    <div class="small fw-semibold text-truncate">Espacio vacío</div>
                                    <div class="small text-secondary text-truncate">Sin objetos</div>
                                    <div class="mt-auto">
                                        <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-secondary btn-sm w-100">Equipar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <p class="small text-secondary mt-2 mb-0">Aún no tienes objetos en el inventario.</p>
            @endif
        </div>

        <div class="col-12 d-flex flex-column mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h3 class="h6 mb-0">Pociones</h3>
                    <p class="small text-secondary mb-0">Usa pociones para curarte o mejorar stats temporalmente.</p>
                </div>
            </div>

            @php
                $potions = $inventory->filter(function ($inv) {
                    return $inv->item->type === 'potion';
                });
            @endphp

            @if ($potions->count() > 0)
                <div class="row g-2">
                    @foreach ($potions as $inv)
                        <div class="col-6 col-lg-4">
                            <div class="card bg-dark border-secondary h-100">
                                <div class="card-body p-2 d-flex flex-column">
                                    <div class="small fw-semibold text-truncate">{{ $inv->item->name ?? 'Poción' }}</div>
                                    <div class="small text-secondary">x{{ $inv->quantity }}</div>
                                    <div class="mt-auto">
                                        @if(str_contains($inv->item->name, 'Curación'))
                                            <form method="POST" action="{{ route('game.inventario.pociones.usar', $inv->item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="heal">
                                                <button type="submit" class="btn btn-success btn-sm w-100">Usar</button>
                                            </form>
                                        @elseif(str_contains($inv->item->name, 'Fuerza'))
                                            <form method="POST" action="{{ route('game.inventario.pociones.usar', $inv->item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="stat">
                                                <input type="hidden" name="stat" value="strength">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Usar</button>
                                            </form>
                                        @elseif(str_contains($inv->item->name, 'Magia'))
                                            <form method="POST" action="{{ route('game.inventario.pociones.usar', $inv->item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="stat">
                                                <input type="hidden" name="stat" value="magic">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Usar</button>
                                            </form>
                                        @elseif(str_contains($inv->item->name, 'Defensa'))
                                            <form method="POST" action="{{ route('game.inventario.pociones.usar', $inv->item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="stat">
                                                <input type="hidden" name="stat" value="defense">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Usar</button>
                                            </form>
                                        @elseif(str_contains($inv->item->name, 'Velocidad'))
                                            <form method="POST" action="{{ route('game.inventario.pociones.usar', $inv->item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="stat">
                                                <input type="hidden" name="stat" value="speed">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Usar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="small text-secondary">No tienes pociones.</p>
            @endif
        </div>
    </div>
</div>
@endsection
