@extends('layouts.game.app')

@section('content')
@php
    $slotLabel = function ($item) {
        $slotKey = $item->slot ?? $item->type ?? '';
        return match ($slotKey) {
            'weapon' => 'Arma',
            'helmet', 'head' => 'Casco',
            'armor', 'chest' => 'Armadura',
            'ring' => 'Anillo',
            'amulet' => 'Talismán',
            'mount' => 'Montura',
            default => $slotKey ?: 'Sin slot',
        };
    };

    $bonusLabel = function ($item) {
        $bonuses = is_array($item->bonuses_json ?? null) ? $item->bonuses_json : [];
        $parts = [];
        if (!empty($bonuses['strength'])) $parts[] = 'Fuerza +' . $bonuses['strength'];
        if (!empty($bonuses['magic'])) $parts[] = 'Magia +' . $bonuses['magic'];
        if (!empty($bonuses['defense'])) $parts[] = 'Defensa +' . $bonuses['defense'];
        if (!empty($bonuses['speed'])) $parts[] = 'Velocidad +' . $bonuses['speed'];
        if (!empty($bonuses['hp'])) $parts[] = 'HP +' . $bonuses['hp'];
        return $parts ? implode(' · ', $parts) : 'Sin bonus';
    };

    $rarityLabel = function ($item) {
        $effects = is_array($item->effects_json ?? null) ? $item->effects_json : [];
        return $effects['rarity'] ?? null;
    };

    $priceLabel = function ($item) {
        return $item->value_gold ?? $item->sell_price ?? 0;
    };
@endphp

<div class="shop-viewport">
    <div class="shop-header">
        <div>
            <h1 class="h5 mb-0">Tienda semanal</h1>
            <div class="small text-secondary">{{ $weekLabel }} · {{ $weekRange }}</div>
        </div>
        @if (Route::has('game.equipamiento.edit'))
            <a href="{{ route('game.equipamiento.edit') }}" class="btn btn-outline-light btn-sm">Ver equipo</a>
        @else
            <a href="{{ route('game.personaje.edit') }}" class="btn btn-outline-light btn-sm">Ver equipo</a>
        @endif
    </div>

    <div class="shop-rows">
        <section class="shop-row">
            <div class="shop-row-title">Armaduras y cascos</div>
            <div class="shop-row-body">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersArmor->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <div class="shop-card">
                            <div class="shop-card-name">{{ $item->name }}</div>
                            <div class="shop-card-meta">{{ $label }}</div>
                            <div class="shop-card-bonus">{{ $bonus }}</div>
                            <div class="shop-card-meta">Precio: {{ $price }}</div>
                            @if ($rarity)
                                <span class="shop-card-badge">{{ strtoupper($rarity) }}</span>
                            @endif
                            <button type="button" class="btn btn-primary btn-sm w-100 mt-auto">Comprar</button>
                        </div>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-name">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-row">
            <div class="shop-row-title">Armas</div>
            <div class="shop-row-body">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersWeapon->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <div class="shop-card">
                            <div class="shop-card-name">{{ $item->name }}</div>
                            <div class="shop-card-meta">{{ $label }}</div>
                            <div class="shop-card-bonus">{{ $bonus }}</div>
                            <div class="shop-card-meta">Precio: {{ $price }}</div>
                            @if ($rarity)
                                <span class="shop-card-badge">{{ strtoupper($rarity) }}</span>
                            @endif
                            <button type="button" class="btn btn-primary btn-sm w-100 mt-auto">Comprar</button>
                        </div>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-name">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-row">
            <div class="shop-row-title">Accesorios</div>
            <div class="shop-row-body">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersAccessory->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <div class="shop-card">
                            <div class="shop-card-name">{{ $item->name }}</div>
                            <div class="shop-card-meta">{{ $label }}</div>
                            <div class="shop-card-bonus">{{ $bonus }}</div>
                            <div class="shop-card-meta">Precio: {{ $price }}</div>
                            @if ($rarity)
                                <span class="shop-card-badge">{{ strtoupper($rarity) }}</span>
                            @endif
                            <button type="button" class="btn btn-primary btn-sm w-100 mt-auto">Comprar</button>
                        </div>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-name">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-row">
            <div class="shop-row-title">Montura de la semana</div>
            <div class="shop-row-body shop-row-body--mount">
                @if ($offerMount)
                    @php
                        $label = $slotLabel($offerMount);
                        $bonus = $bonusLabel($offerMount);
                        $price = $priceLabel($offerMount);
                        $rarity = $rarityLabel($offerMount);
                    @endphp
                    <div class="shop-card">
                        <div class="shop-card-name">{{ $offerMount->name }}</div>
                        <div class="shop-card-meta">{{ $label }}</div>
                        <div class="shop-card-bonus">{{ $bonus }}</div>
                        <div class="shop-card-meta">Precio: {{ $price }}</div>
                        @if ($rarity)
                            <span class="shop-card-badge">{{ strtoupper($rarity) }}</span>
                        @endif
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-auto">Comprar</button>
                    </div>
                @else
                    <div class="shop-card shop-card--empty">
                        <div class="shop-card-name">Sin montura seleccionada</div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    @if ($offersArmor->isEmpty() && $offersWeapon->isEmpty() && $offersAccessory->isEmpty() && !$offerMount)
        <div class="small text-secondary mt-2">No hay objetos cargados en la tienda. Ejecuta las migraciones y el seeder de items.</div>
    @endif
</div>
@endsection
