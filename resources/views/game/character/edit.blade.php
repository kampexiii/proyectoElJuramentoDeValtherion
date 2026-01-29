@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 align-items-stretch h-100">
        <div class="col-12 col-xl-6">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm h-100">
                <div class="card-header border-secondary bg-dark text-center py-2">Editar personaje</div>
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

                    @php
                        $stats = $character->stats_json ?? [];
                    @endphp

                    <form action="{{ route('game.personaje.update') }}" method="POST" class="d-grid gap-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $character->name) }}" required>
                        </div>

                        <div>
                            <label for="race_id" class="form-label">Raza</label>
                            @if ($races->count() > 0)
                                <select class="form-select" id="race_id" name="race_id">
                                    <option value="">Selecciona una raza</option>
                                    @foreach ($races as $race)
                                        <option value="{{ $race->id }}" @selected(old('race_id', $character->race_id) == $race->id)>{{ $race->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" value="Pendiente" disabled>
                            @endif
                        </div>

                        <div>
                            <label class="form-label">Stats (temporal)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="stat_fuerza" class="form-label small">Fuerza</label>
                                    <input type="number" class="form-control" id="stat_fuerza" name="stats[fuerza]" value="{{ old('stats.fuerza', $stats['fuerza'] ?? '') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_magia" class="form-label small">Magia</label>
                                    <input type="number" class="form-control" id="stat_magia" name="stats[magia]" value="{{ old('stats.magia', $stats['magia'] ?? '') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_defensa" class="form-label small">Defensa</label>
                                    <input type="number" class="form-control" id="stat_defensa" name="stats[defensa]" value="{{ old('stats.defensa', $stats['defensa'] ?? '') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_velocidad" class="form-label small">Velocidad</label>
                                    <input type="number" class="form-control" id="stat_velocidad" name="stats[velocidad]" value="{{ old('stats.velocidad', $stats['velocidad'] ?? '') }}" min="0" max="999">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>

                    <form action="{{ route('game.personaje.destroy') }}" method="POST" class="mt-auto" onsubmit="return confirm('¿Seguro que quieres borrar tu personaje?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">Borrar personaje</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm h-100">
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
                                                <span class="badge bg-warning text-dark">Fija</span>
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

                    <div>
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
                        @elseif ($items->count() > 0)
                            <p class="small text-secondary mb-0">Aún no tienes objetos en inventario.</p>
                        @else
                            <p class="small text-secondary mb-0">Aún no hay objetos cargados. Cuando estén en la base de datos, podrás equiparlos aquí.</p>
                        @endif
                    </div>

                    <div>
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
                            <div class="alert alert-secondary mb-0">Aún no hay objetos cargados. Cuando estén en la base de datos, podrás equiparlos aquí.</div>
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
