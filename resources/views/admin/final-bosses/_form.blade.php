<div class="card bg-zinc-900 border-secondary text-white shadow-sm">
    <div class="card-body p-3">
        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" class="d-grid gap-2">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div>
                <label class="form-label" for="boss_name">Nombre</label>
                <input id="boss_name" name="name" class="form-control form-control-sm" value="{{ old('name', $boss->name ?? '') }}" required>
            </div>

            <div>
                <label class="form-label" for="boss_slug">Slug</label>
                <input id="boss_slug" name="slug" class="form-control form-control-sm" value="{{ old('slug', $boss->slug ?? '') }}" required>
            </div>

            <div>
                <label class="form-label" for="boss_lore">Lore</label>
                <textarea id="boss_lore" name="lore" rows="4" class="form-control form-control-sm">{{ old('lore', $boss->lore ?? '') }}</textarea>
            </div>

            <div>
                <label class="form-label" for="boss_sprite">Sprite (ruta)</label>
                <input
                    id="boss_sprite"
                    name="sprite_path"
                    class="form-control form-control-sm"
                    placeholder="/assets/bosses/{{ old('slug', $boss->slug ?? 'slug-del-boss') }}.png"
                    value="{{ old('sprite_path', $boss->sprite_path ?? '') }}"
                >
            </div>

            @php
                $stats = old('base_stats_json', $boss->base_stats_json ?? []);
            @endphp

            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="boss_hp">HP</label>
                    <input id="boss_hp" name="base_stats_json[hp]" type="number" min="0" class="form-control form-control-sm" value="{{ $stats['hp'] ?? '' }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="boss_damage">Daño</label>
                    <input id="boss_damage" name="base_stats_json[damage]" type="number" min="0" class="form-control form-control-sm" value="{{ $stats['damage'] ?? '' }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="boss_defense">Defensa</label>
                    <input id="boss_defense" name="base_stats_json[defense]" type="number" min="0" class="form-control form-control-sm" value="{{ $stats['defense'] ?? '' }}" required>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-info btn-sm">{{ $submitLabel }}</button>
        </form>
    </div>
</div>
