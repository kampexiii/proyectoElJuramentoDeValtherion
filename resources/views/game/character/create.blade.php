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
                                        <option value="{{ $race->id }}" @selected(old('race_id') == $race->id)>{{ $race->name }}</option>
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
                                    <input type="number" class="form-control" id="stat_fuerza" name="stats[fuerza]" value="{{ old('stats.fuerza') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_magia" class="form-label small">Magia</label>
                                    <input type="number" class="form-control" id="stat_magia" name="stats[magia]" value="{{ old('stats.magia') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_defensa" class="form-label small">Defensa</label>
                                    <input type="number" class="form-control" id="stat_defensa" name="stats[defensa]" value="{{ old('stats.defensa') }}" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="stat_velocidad" class="form-label small">Velocidad</label>
                                    <input type="number" class="form-control" id="stat_velocidad" name="stats[velocidad]" value="{{ old('stats.velocidad') }}" min="0" max="999">
                                </div>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_mount" name="has_mount" value="1" @checked(old('has_mount'))>
                            <label class="form-check-label" for="has_mount">Tiene montura</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar personaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
