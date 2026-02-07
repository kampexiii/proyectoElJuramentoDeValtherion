<div class="card bg-zinc-900 border-secondary text-white shadow-sm">
    <div class="card-body p-2 d-grid gap-2">
        <div>
            <label class="form-label" for="mission_title">Titulo</label>
            <input id="mission_title" name="title" class="form-control form-control-sm" value="{{ old('title', $mission->title ?? '') }}" required>
        </div>

        <div>
            <label class="form-label" for="mission_slug">Slug</label>
            <input id="mission_slug" name="slug" class="form-control form-control-sm" value="{{ old('slug', $mission->slug ?? '') }}" required>
        </div>

        <div class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label" for="mission_status">Estado</label>
                <select id="mission_status" name="status" class="form-select form-select-sm" required>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option['value'] }}" @selected(old('status', $mission->status->value ?? 'draft') === $option['value'])>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="mission_repeatable">Repeatable</label>
                <div class="form-check">
                    <input id="mission_repeatable" name="repeatable" type="checkbox" value="1" class="form-check-input" @checked(old('repeatable', $mission->repeatable ?? false))>
                    <label class="form-check-label" for="mission_repeatable">Se puede repetir</label>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="mission_points">Puntos base</label>
                <input id="mission_points" name="base_race_points" type="number" min="0" class="form-control form-control-sm" value="{{ old('base_race_points', $mission->base_race_points ?? 0) }}" required>
            </div>
        </div>

        <div>
            <label class="form-label" for="mission_boss">Boss final</label>
            <select id="mission_boss" name="final_boss_id" class="form-select form-select-sm">
                <option value="">Sin boss asignado</option>
                @foreach ($bosses as $boss)
                    <option value="{{ $boss->id }}" @selected(old('final_boss_id', $mission->final_boss_id ?? '') == $boss->id)>
                        {{ $boss->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" for="mission_intro">Intro</label>
            <textarea id="mission_intro" name="intro_text" rows="3" class="form-control form-control-sm" required>{{ old('intro_text', $mission->intro_text ?? '') }}</textarea>
        </div>

        <div>
            <label class="form-label" for="mission_context">Contexto</label>
            <textarea id="mission_context" name="context_text" rows="3" class="form-control form-control-sm">{{ old('context_text', $mission->context_text ?? '') }}</textarea>
        </div>
    </div>
</div>
