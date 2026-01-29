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

            @if (!empty($inventory) && $inventory->count() > 0)
                <div class="row g-2">
                    @foreach ($inventory->take(6) as $inv)
                        <div class="col-6 col-lg-4">
                            <div class="card bg-dark border-secondary h-100">
                                <div class="card-body p-2 d-flex flex-column">
                                    <div class="small fw-semibold text-truncate">{{ $inv->item->name ?? 'Objeto' }}</div>
                                    <div class="small text-secondary text-truncate">{{ $inv->item->slot ?? $inv->item->type ?? 'Sin slot' }}</div>
                                    <div class="small text-secondary">x{{ $inv->quantity }}</div>
                                    <div class="mt-auto">
                                        <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-light btn-sm w-100">Equipar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($inventory->count() > 6)
                    <p class="small text-secondary mt-2 mb-0">Ver más (próximamente).</p>
                @endif
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
    </div>
</div>
@endsection
