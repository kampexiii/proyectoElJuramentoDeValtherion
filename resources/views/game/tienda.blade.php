@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-12 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h2 class="h5 mb-0">Tienda</h2>
                    <p class="small text-secondary mb-0">Objetos disponibles (base funcional).</p>
                </div>
                <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-light btn-sm">Ver equipo</a>
            </div>

            @if (!empty($items) && $items->count() > 0)
                <div class="row g-2">
                    @foreach ($items->take(6) as $item)
                        <div class="col-6 col-lg-4">
                            <div class="card bg-dark border-secondary h-100">
                                <div class="card-body p-2 d-flex flex-column">
                                    <div class="small fw-semibold text-truncate">{{ $item->name }}</div>
                                    <div class="small text-secondary text-truncate">{{ $item->slot ?? $item->type ?? 'Sin slot' }}</div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-primary btn-sm w-100">Comprar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($items->count() > 6)
                    <p class="small text-secondary mt-2 mb-0">Ver más (próximamente).</p>
                @endif
            @else
                <div class="row g-2">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="col-6 col-lg-4">
                            <div class="card bg-dark border-secondary h-100">
                                <div class="card-body p-2 d-flex flex-column">
                                    <div class="small fw-semibold text-truncate">Objeto pendiente</div>
                                    <div class="small text-secondary text-truncate">Sin datos</div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" disabled>Comprar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <p class="small text-secondary mt-2 mb-0">Aún no hay objetos cargados en la tienda.</p>
            @endif
        </div>
    </div>
</div>
@endsection
