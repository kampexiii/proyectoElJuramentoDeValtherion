@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 align-items-stretch edit-row">
        <div class="col-12 col-xl-6">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm edit-card">
                <div class="card-header border-secondary bg-dark text-center py-2">Personaje</div>
                <div class="card-body p-3 d-flex flex-column gap-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary">Nombre</span>
                        <span class="fw-semibold">{{ $character->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary">Raza</span>
                        <span class="fw-semibold">{{ $character->race->name ?? 'Pendiente' }}</span>
                    </div>


                    <div>
                        <h6 class="mb-2">Stats actuales</h6>
                        @php
                            $labels = [
                                'fuerza' => 'Fuerza',
                                'magia' => 'Magia',
                                'defensa' => 'Defensa',
                                'velocidad' => 'Velocidad',
                            ];
                        @endphp
                        <div class="d-grid gap-2">
                            @foreach ($labels as $key => $label)
                                @php
                                    $stat = $statsView[$key] ?? ['valor' => 0, 'max' => 1, 'clase' => 'hx-bar-0', 'color' => 'bg-danger'];
                                @endphp
                                <div>
                                    <div class="d-flex justify-content-between small">
                                        <span>{{ $label }}</span>
                                        <span>{{ $stat['valor'] }}/{{ $stat['max'] }}</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar {{ $stat['color'] }} {{ $stat['clase'] }}" role="progressbar"></div>
                                    </div>
                                </div>
                            @endforeach
                            <!-- HP Bar -->
                            <div>
                                <div class="d-flex justify-content-between small">
                                    <span>HP</span>
                                    <span>{{ $character->hp_current }}/{{ $character->hp_max }}</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success hx-bar-{{ (int) round(($character->hp_current / max($character->hp_max, 1)) * 100) }}" role="progressbar"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($spriteUrl)
                        <div class="text-center d-none d-xl-block">
                            <img src="{{ $spriteUrl }}" alt="Sprite del personaje" class="edit-sprite-img">
                        </div>
                    @endif

                    <form action="{{ route('game.personaje.destroy') }}" method="POST" class="mt-auto" onsubmit="return confirm('¿Seguro que quieres borrar tu personaje?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">Borrar personaje</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm edit-card">
                <div class="card-header border-secondary bg-dark text-center py-2">Equipo e inventario</div>
                <div class="card-body p-3 d-flex flex-column gap-3">
                    @if (session('status'))
                        <div class="alert alert-success mb-0">{{ session('status') }}</div>
                    @endif

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Equipo actual</h6>
                            <span class="small text-secondary">Slots</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($slots as $slotKey => $slotLabel)
                                @php
                                    $equipped = $equipment->get($slotKey);
                                @endphp
                                <div class="list-group-item bg-transparent text-white border-secondary px-0">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <span class="fw-semibold">{{ $slotLabel }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($slotKey === 'mount' && $character->mount)
                                                <span class="text-truncate">{{ $character->mount->name }}</span>
                                            @elseif ($equipped && $equipped->item)
                                                <span class="text-truncate">{{ $equipped->item->name }}</span>
                                                <form action="{{ route('game.personaje.desequipar') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="slot" value="{{ $slotKey }}">
                                                    <button type="submit" class="btn btn-outline-light btn-sm">Quitar</button>
                                                </form>
                                            @else
                                                <span class="text-secondary">Vacío</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-none d-xl-block">
                        <h6 class="mb-2">Inventario rápido</h6>
                        @if ($inventory->count() > 0)
                            <div class="row g-2">
                                @foreach ($inventory->take(6) as $inv)
                                    <div class="col-6">
                                        <div class="card bg-dark border-secondary h-100">
                                            <div class="card-body p-2">
                                                <div class="small fw-semibold text-truncate">{{ $inv->item->name ?? 'Objeto' }}</div>
                                                <div class="small text-secondary text-truncate">
                                                    {{ $inv->quantity }} uds · {{ $inv->item->slot ?? $inv->item->type ?? 'sin slot' }}
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
                            <p class="small text-secondary mb-0">Aún no tienes objetos en inventario.</p>
                        @endif
                    </div>

                    <div class="d-none d-xl-block">
                        <h6 class="mb-2">Equipar objeto</h6>
                        @if ($items->count() > 0)
                            <form action="{{ route('game.personaje.equipar') }}" method="POST" class="d-grid gap-2">
                                @csrf
                                <div>
                                    <label for="equip-slot" class="form-label small">Slot</label>
                                    <select class="form-select" id="equip-slot" name="slot" required>
                                        @foreach ($slots as $slotKey => $slotLabel)
                                            <option value="{{ $slotKey }}" @disabled($slotKey === 'mount' && $character->mount)>{{ $slotLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="equip-item" class="form-label small">Objeto</label>
                                    <select class="form-select" id="equip-item" name="item_id" required>
                                        @foreach ($items as $item)
                                            @php
                                                $itemSlot = $item->slot ?? $item->type ?? '';
                                            @endphp
                                            <option value="{{ $item->id }}" data-slot="{{ $itemSlot }}">
                                                {{ $item->name }} @if($itemSlot) — {{ $itemSlot }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Equipar</button>
                            </form>
                        @else
                            <div class="alert alert-secondary mb-0">Aún no tienes objetos para equipar.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const slotSelect = document.getElementById('equip-slot');
        const itemSelect = document.getElementById('equip-item');

        if (!slotSelect || !itemSelect) {
            return;
        }

        function filtrarItems() {
            const slot = (slotSelect.value || '').toLowerCase();
            const options = Array.from(itemSelect.options);
            let firstVisible = null;

            options.forEach((option) => {
                const itemSlot = (option.dataset.slot || '').toLowerCase();
                const visible = !slot || itemSlot === slot;
                option.hidden = !visible;
                if (visible && !firstVisible) {
                    firstVisible = option;
                }
            });

            if (firstVisible) {
                itemSelect.value = firstVisible.value;
            }
        }

        slotSelect.addEventListener('change', filtrarItems);
        filtrarItems();
    })();
</script>
@endsection
