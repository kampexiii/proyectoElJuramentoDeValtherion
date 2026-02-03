@extends('layouts.game.app')

@section('content')
@php
    // Helpers de texto para mantener la tienda compacta y legible.
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

<div class="shop-viewport game-viewport">
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
        @php // Secciones fijas, misma estructura en tablet/portátil/desktop. @endphp
        <section class="shop-section">
            <div class="shop-section-header">
                <h2 class="shop-section-title">Armaduras y cascos</h2>
            </div>
            <div class="shop-grid">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersArmor->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <article class="shop-card">
                            <div class="shop-card-header">
                                <h3 class="shop-card-name" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body">
                                <div class="shop-card-type" title="{{ $label }}">{{ $label }}</div>
                                <div class="shop-card-bonus" title="{{ $bonus }}">{{ $bonus }}</div>
                            </div>
                            <div class="shop-card-footer">
                                <div class="shop-card-price">Precio: {{ $price }}</div>
                                <button type="button" class="btn btn-primary btn-sm">Comprar</button>
                            </div>
                        </article>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-empty-text">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-section">
            <div class="shop-section-header">
                <h2 class="shop-section-title">Armas</h2>
            </div>
            <div class="shop-grid">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersWeapon->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <article class="shop-card">
                            <div class="shop-card-header">
                                <h3 class="shop-card-name" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body">
                                <div class="shop-card-type" title="{{ $label }}">{{ $label }}</div>
                                <div class="shop-card-bonus" title="{{ $bonus }}">{{ $bonus }}</div>
                            </div>
                            <div class="shop-card-footer">
                                <div class="shop-card-price">Precio: {{ $price }}</div>
                                <button type="button" class="btn btn-primary btn-sm">Comprar</button>
                            </div>
                        </article>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-empty-text">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-section">
            <div class="shop-section-header">
                <h2 class="shop-section-title">Accesorios</h2>
            </div>
            <div class="shop-grid">
                @for ($i = 0; $i < 3; $i++)
                    @php $item = $offersAccessory->get($i); @endphp
                    @if ($item)
                        @php
                            $label = $slotLabel($item);
                            $bonus = $bonusLabel($item);
                            $price = $priceLabel($item);
                            $rarity = $rarityLabel($item);
                        @endphp
                        <article class="shop-card">
                            <div class="shop-card-header">
                                <h3 class="shop-card-name" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body">
                                <div class="shop-card-type" title="{{ $label }}">{{ $label }}</div>
                                <div class="shop-card-bonus" title="{{ $bonus }}">{{ $bonus }}</div>
                            </div>
                            <div class="shop-card-footer">
                                <div class="shop-card-price">Precio: {{ $price }}</div>
                                <button type="button" class="btn btn-primary btn-sm">Comprar</button>
                            </div>
                        </article>
                    @else
                        <div class="shop-card shop-card--empty">
                            <div class="shop-card-empty-text">No disponible</div>
                        </div>
                    @endif
                @endfor
            </div>
        </section>

        <section class="shop-section">
            <div class="shop-section-header">
                <h2 class="shop-section-title">Montura de la semana</h2>
            </div>
            <div class="shop-grid shop-grid--mount">
                @if ($offerMount)
                    @php
                        $label = $slotLabel($offerMount);
                        $bonus = $bonusLabel($offerMount);
                        $price = $priceLabel($offerMount);
                        $rarity = $rarityLabel($offerMount);
                    @endphp
                    <article class="shop-card">
                        <div class="shop-card-header">
                            <h3 class="shop-card-name" title="{{ $offerMount->name }}">{{ $offerMount->name }}</h3>
                            @if ($rarity)
                                <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                            @endif
                        </div>
                        <div class="shop-card-body">
                            <div class="shop-card-type" title="{{ $label }}">{{ $label }}</div>
                            <div class="shop-card-bonus" title="{{ $bonus }}">{{ $bonus }}</div>
                        </div>
                        <div class="shop-card-footer">
                            <div class="shop-card-price">Precio: {{ $price }}</div>
                            <button type="button" class="btn btn-primary btn-sm">Comprar</button>
                        </div>
                    </article>
                @else
                    <div class="shop-card shop-card--empty">
                        <div class="shop-card-empty-text">Sin montura seleccionada</div>
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
