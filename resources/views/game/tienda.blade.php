@extends('layouts.game.app')

@section('content')
@php
    // Helpers de texto para etiquetas de tienda.
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
        // Prioriza bonuses_json y fallback a bonus_* legacy.
        $bonuses = $item->bonuses_json ?? [];
        if (is_string($bonuses)) {
            $decoded = json_decode($bonuses, true);
            $bonuses = is_array($decoded) ? $decoded : [];
        }
        if (isset($bonuses['bonuses']) && is_array($bonuses['bonuses'])) {
            $bonuses = $bonuses['bonuses'];
        }
        if (isset($bonuses['stats']) && is_array($bonuses['stats'])) {
            $bonuses = $bonuses['stats'];
        }

        $strength = (int) ($bonuses['fuerza'] ?? $bonuses['strength'] ?? $bonuses['bonus_strength'] ?? $item->bonus_strength ?? 0);
        $magic = (int) ($bonuses['magia'] ?? $bonuses['magic'] ?? $bonuses['bonus_magic'] ?? $item->bonus_magic ?? 0);
        $defense = (int) ($bonuses['defensa'] ?? $bonuses['defense'] ?? $bonuses['bonus_defense'] ?? $item->bonus_defense ?? 0);
        $speed = (int) ($bonuses['velocidad'] ?? $bonuses['speed'] ?? $bonuses['bonus_speed'] ?? $item->bonus_speed ?? 0);
        $hp = (int) ($bonuses['hp'] ?? $bonuses['vida'] ?? $bonuses['bonus_hp'] ?? $item->bonus_hp ?? 0);

        $parts = [];
        if ($strength > 0) $parts[] = 'Fuerza +' . $strength;
        if ($magic > 0) $parts[] = 'Magia +' . $magic;
        if ($defense > 0) $parts[] = 'Defensa +' . $defense;
        if ($speed > 0) $parts[] = 'Velocidad +' . $speed;
        if ($hp > 0) $parts[] = 'HP +' . $hp;

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

<main class="shop-viewport game-viewport shop-ui">
    @if ($errors->has('shop'))
        <div class="alert alert-danger small mb-2">{{ $errors->first('shop') }}</div>
    @endif

    @if (session('shop-status'))
        <div class="alert alert-success small mb-2">{{ session('shop-status') }}</div>
    @endif

    <div class="shop-rows">
        @php // Secciones fijas con la misma estructura responsiva. @endphp
        <section class="shop-section">
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
                            <div class="shop-card-header min-w-0">
                                <h3 class="shop-card-name shop-title" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body min-w-0">
                                <div class="shop-card-bonus shop-meta" title="{{ $bonus }}">{{ $bonus }}</div>
                                <div class="shop-card-type shop-meta" title="{{ $label }}">{{ $label }}</div>
                            </div>
                            <div class="shop-card-footer min-w-0">
                                <div class="shop-card-price shop-price">Precio: {{ $price }}</div>
                                <form method="POST" action="{{ route('game.tienda.comprar') }}">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm shop-btn">Comprar</button>
                                </form>
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
                            <div class="shop-card-header min-w-0">
                                <h3 class="shop-card-name shop-title" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body min-w-0">
                                <div class="shop-card-bonus shop-meta" title="{{ $bonus }}">{{ $bonus }}</div>
                                <div class="shop-card-type shop-meta" title="{{ $label }}">{{ $label }}</div>
                            </div>
                            <div class="shop-card-footer min-w-0">
                                <div class="shop-card-price shop-price">Precio: {{ $price }}</div>
                                <form method="POST" action="{{ route('game.tienda.comprar') }}">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm shop-btn">Comprar</button>
                                </form>
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
                            <div class="shop-card-header min-w-0">
                                <h3 class="shop-card-name shop-title" title="{{ $item->name }}">{{ $item->name }}</h3>
                                @if ($rarity)
                                    <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                                @endif
                            </div>
                            <div class="shop-card-body min-w-0">
                                <div class="shop-card-bonus shop-meta" title="{{ $bonus }}">{{ $bonus }}</div>
                                <div class="shop-card-type shop-meta" title="{{ $label }}">{{ $label }}</div>
                            </div>
                            <div class="shop-card-footer min-w-0">
                                <div class="shop-card-price shop-price">Precio: {{ $price }}</div>
                                <form method="POST" action="{{ route('game.tienda.comprar') }}">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm shop-btn">Comprar</button>
                                </form>
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
            <div class="shop-grid shop-grid--mount">
                @if ($offerMount)
                    @php
                        $label = $slotLabel($offerMount);
                        $bonus = $bonusLabel($offerMount);
                        $price = $priceLabel($offerMount);
                        $rarity = $rarityLabel($offerMount);
                    @endphp
                    <article class="shop-card">
                        <div class="shop-card-header min-w-0">
                            <h3 class="shop-card-name shop-title" title="{{ $offerMount->name }}">{{ $offerMount->name }}</h3>
                            @if ($rarity)
                                <span class="shop-card-rarity">{{ strtoupper($rarity) }}</span>
                            @endif
                        </div>
                        <div class="shop-card-body min-w-0">
                            <div class="shop-card-bonus shop-meta" title="{{ $bonus }}">{{ $bonus }}</div>
                            <div class="shop-card-type shop-meta" title="{{ $label }}">{{ $label }}</div>
                        </div>
                        <div class="shop-card-footer min-w-0">
                            <div class="shop-card-price shop-price">Precio: {{ $price }}</div>
                            <form method="POST" action="{{ route('game.tienda.comprar') }}">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $offerMount->id }}">
                                <button type="submit" class="btn btn-primary btn-sm shop-btn">Comprar</button>
                            </form>
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
</main>
@endsection
