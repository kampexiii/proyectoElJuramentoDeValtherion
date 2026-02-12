@extends('layouts.game.app')

@section('content')
@php
    $slots = [
        'helmet' => ['label' => 'Cascos', 'field' => 'helmet_item_id', 'empty' => 'Sin casco'],
        'armor' => ['label' => 'Armaduras', 'field' => 'armor_item_id', 'empty' => 'Sin armadura'],
        'weapon' => ['label' => 'Armas', 'field' => 'weapon_item_id', 'empty' => 'Sin arma'],
        'ring' => ['label' => 'Anillos', 'field' => 'ring_item_id', 'empty' => 'Sin anillo'],
        'amulet' => ['label' => 'Colgantes', 'field' => 'amulet_item_id', 'empty' => 'Sin colgante'],
        'mount' => ['label' => 'Montura', 'field' => 'mount_item_id', 'empty' => 'Sin montura'],
    ];
@endphp

<!-- Layout principal entre navbars (tienda) -->
<main class="app-main equipamiento-viewport game-viewport">
    <div class="mb-3">
        <h2 class="h6">Stats actuales</h2>
        @php
            $labels = [
                'hp' => 'HP',
                'attack' => 'Fuerza',
                'defense' => 'Defensa',
                'speed' => 'Velocidad',
                'magic' => 'Magia',
            ];
            $statsBase = $statsBase ?? [];
        @endphp
        @if (!empty($statsBase))
            <div class="small text-secondary mb-1">
                @foreach ($labels as $stat => $label)
                    <span class="me-2">{{ $label }} base: <strong>{{ $statsBase[$stat] ?? 0 }}</strong></span>
                @endforeach
            </div>
        @endif
        <div id="stats-preview" class="mb-2 small text-secondary">
            @php $stats = method_exists(auth()->user()?->character, 'effectiveStats') ? auth()->user()->character->effectiveStats() : []; @endphp
            @foreach($stats as $stat => $valor)
                <span class="me-2">{{ ucfirst($stat) }}: <strong>{{ $valor }}</strong></span>
            @endforeach
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('js/equipamiento-stats-preview.js') }}"></script>
    <script>
        // Habilita el boton de pocion segun seleccion.
        (function () {
            const select = document.getElementById('potion-select');
            const button = document.getElementById('potion-submit');
            if (!select || !button) {
                return;
            }
            const syncState = () => {
                button.disabled = !select.value;
            };
            select.addEventListener('change', syncState);
            syncState();
        })();
    </script>
    @endpush
    <div class="equipamiento-header">
        <div>
            <h1 class="h5 mb-0">Armería</h1>
            <p class="small text-secondary mb-0">Elige el equipo que llevarás al combate.</p>
        </div>
        <div class="equipamiento-actions">
            <form id="potion-form" method="POST" action="{{ route('game.pociones.usar') }}" class="equipamiento-potions">
                @csrf
                <label for="potion-select" class="form-label">Usar poción</label>
                <div class="equipamiento-potions-row">
                    <select id="potion-select" name="item_id" class="form-select form-select-sm" @disabled($potions->isEmpty())>
                        <option value="">Selecciona una poción</option>
                        @foreach ($potions as $potion)
                            @php
                                $item = $potion->item;
                                $label = $item?->name ?? 'Poción';
                                $cantidad = $potion->quantity ?? 0;
                            @endphp
                            <option value="{{ $item->id }}">{{ $label }} — Cantidad: {{ $cantidad }}</option>
                        @endforeach
                    </select>
                    <button id="potion-submit" type="submit" class="btn btn-outline-light btn-sm" @disabled($potions->isEmpty())>Usar poción</button>
                </div>
                @if ($potions->isEmpty())
                    <div class="small text-secondary">No tienes pociones.</div>
                @endif
            </form>
            <button form="equipamiento-form" type="submit" class="btn btn-primary btn-sm">Guardar equipamiento</button>
        </div>
    </div>

    @if ($errors->has('equipamiento'))
        <div class="alert alert-danger small mb-2">{{ $errors->first('equipamiento') }}</div>
    @endif

    @if (session('status'))
        <div class="alert alert-success small mb-2">{{ session('status') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success small mb-2">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger small mb-2">{{ session('error') }}</div>
    @endif

    <form id="equipamiento-form" method="POST" action="{{ route('game.equipamiento.update') }}" class="equipamiento-grid">
        @csrf

        @foreach ($slots as $slot => $data)
            @if ($slot !== 'mount' || $showMount)
                @php
                    $items = $options[$slot] ?? collect();
                    $currentId = $current[$slot] ?? null;
                @endphp
                <div class="equipamiento-card">
                    <label for="{{ $data['field'] }}" class="form-label">{{ $data['label'] }}</label>
                    <select id="{{ $data['field'] }}" name="{{ $data['field'] }}" class="form-select form-select-sm">
                        <option value="">{{ $data['empty'] }}</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected($currentId == $item->id)>
                                {{ $item->name }}
                                {{-- Monturas: todas son equipables --}}
                            </option>
                        @endforeach
                    </select>
                    @error($data['field'])
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                    @if ($items->isEmpty())
                        <div class="small text-secondary">No posees objetos para este hueco.</div>
                    @endif
                </div>
            @endif
        @endforeach
    </form>
</main>
@endsection
