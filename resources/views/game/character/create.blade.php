@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-12 col-lg-6">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-2">Crear personaje</div>
                <div class="card-body p-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('game.personaje.store') }}" method="POST" class="d-grid gap-3">
                        @csrf

                        <div>
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div>
                            <label for="race_id" class="form-label">Raza</label>
                            @if ($races->count() > 0)
                                <select class="form-select" id="race_id" name="race_id">
                                    <option value="">Selecciona una raza</option>
                                    @foreach ($races as $race)
                                        <option
                                            value="{{ $race->id }}"
                                            @selected(old('race_id') == $race->id)
                                            data-sprite="{{ $race->sprite }}"
                                            data-fuerza="{{ $race->base_strength }}"
                                            data-magia="{{ $race->base_magic }}"
                                            data-defensa="{{ $race->base_defense }}"
                                            data-velocidad="{{ $race->base_speed }}"
                                        >
                                            {{ $race->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" value="Pendiente" disabled>
                            @endif
                        </div>

                        <div
                            class="character-preview"
                            data-max-fuerza="{{ $statMax['fuerza'] ?? 1 }}"
                            data-max-magia="{{ $statMax['magia'] ?? 1 }}"
                            data-max-defensa="{{ $statMax['defensa'] ?? 1 }}"
                            data-max-velocidad="{{ $statMax['velocidad'] ?? 1 }}"
                        >
                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-md-5 text-center">
                                    <div class="character-sprite-frame">
                                        <img
                                            src="/assets/sprites/razas/humanos.png"
                                            class="img-fluid character-sprite opacity-50"
                                            alt="Sprite de raza"
                                            id="race-sprite"
                                        >
                                    </div>
                                    <p class="small text-secondary mb-0" id="race-name">Selecciona una raza</p>
                                </div>
                                <div class="col-12 col-md-7">
                                    <div class="d-grid gap-2">
                                        <div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Fuerza</span>
                                                <span id="stat-fuerza-val">-</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger hx-bar-0" id="stat-fuerza-bar" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Magia</span>
                                                <span id="stat-magia-val">-</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger hx-bar-0" id="stat-magia-bar" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Defensa</span>
                                                <span id="stat-defensa-val">-</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger hx-bar-0" id="stat-defensa-bar" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Velocidad</span>
                                                <span id="stat-velocidad-val">-</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger hx-bar-0" id="stat-velocidad-bar" role="progressbar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar personaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const select = document.getElementById('race_id');
        const sprite = document.getElementById('race-sprite');
        const nameLabel = document.getElementById('race-name');
        const preview = document.querySelector('.character-preview');

        const bars = {
            fuerza: document.getElementById('stat-fuerza-bar'),
            magia: document.getElementById('stat-magia-bar'),
            defensa: document.getElementById('stat-defensa-bar'),
            velocidad: document.getElementById('stat-velocidad-bar'),
        };

        const values = {
            fuerza: document.getElementById('stat-fuerza-val'),
            magia: document.getElementById('stat-magia-val'),
            defensa: document.getElementById('stat-defensa-val'),
            velocidad: document.getElementById('stat-velocidad-val'),
        };

        const max = {
            fuerza: parseInt(preview.dataset.maxFuerza || '1', 10),
            magia: parseInt(preview.dataset.maxMagia || '1', 10),
            defensa: parseInt(preview.dataset.maxDefensa || '1', 10),
            velocidad: parseInt(preview.dataset.maxVelocidad || '1', 10),
        };

        const widthClasses = [
            'hx-bar-0', 'hx-bar-10', 'hx-bar-20', 'hx-bar-30', 'hx-bar-40',
            'hx-bar-50', 'hx-bar-60', 'hx-bar-70', 'hx-bar-80', 'hx-bar-90', 'hx-bar-100',
        ];

        const colorClasses = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];

        function colorPorcentaje(ratio) {
            if (ratio >= 0.75) {
                return 'bg-success';
            }
            if (ratio >= 0.5) {
                return 'bg-info';
            }
            if (ratio >= 0.25) {
                return 'bg-warning';
            }
            return 'bg-danger';
        }

        function setBar(stat, value) {
            const maxVal = max[stat] || 1;
            const ratio = Math.max(0, Math.min(1, value / maxVal));
            const step = Math.round(ratio * 10) * 10;
            const className = 'hx-bar-' + step;

            bars[stat].classList.remove(...widthClasses);
            bars[stat].classList.add(className);
            bars[stat].classList.remove(...colorClasses);
            bars[stat].classList.add(colorPorcentaje(ratio));
            values[stat].textContent = value > 0 ? value : '-';
        }

        function resetBars() {
            Object.keys(bars).forEach((stat) => {
                bars[stat].classList.remove(...widthClasses);
                bars[stat].classList.add('hx-bar-0');
                bars[stat].classList.remove(...colorClasses);
                bars[stat].classList.add('bg-danger');
                values[stat].textContent = '-';
            });
        }

        function updatePreview() {
            if (!select) {
                return;
            }

            const option = select.options[select.selectedIndex];
            const raceName = option ? option.text.trim() : '';
            const spritePath = option ? option.dataset.sprite : '';

            if (!option || !option.value) {
                sprite.src = '/assets/sprites/razas/humanos.png';
                sprite.classList.add('opacity-50');
                nameLabel.textContent = 'Selecciona una raza';
                resetBars();
                return;
            }

            sprite.src = spritePath || '/assets/sprites/razas/humanos.png';
            sprite.classList.remove('opacity-50');
            nameLabel.textContent = raceName;

            setBar('fuerza', parseInt(option.dataset.fuerza || '0', 10));
            setBar('magia', parseInt(option.dataset.magia || '0', 10));
            setBar('defensa', parseInt(option.dataset.defensa || '0', 10));
            setBar('velocidad', parseInt(option.dataset.velocidad || '0', 10));
        }

        if (select) {
            select.addEventListener('change', updatePreview);
        }

        if (sprite) {
            sprite.addEventListener('error', function () {
                sprite.src = '/assets/sprites/razas/humanos.png';
            });
        }

        updatePreview();
    })();
</script>
@endsection
