@extends('layouts.game.app')

@section('content')
<div class="home-viewport">
    <div class="home-content flex-grow-1">
        <div class="row g-0 flex-grow-1 h-100">
            <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
                <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden hx-card-bg">
                    <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Códigos de recompensa</div>
                    <div class="card-body d-flex flex-column justify-content-center p-3 overflow-hidden">
                        @if (!$hasRewardTable)
                            <div class="alert alert-warning small mb-0 text-center">Falta ejecutar migraciones para crear la tabla de códigos.</div>
                        @else
                            @if ($errors->has('code') || $errors->has('reward'))
                                <div class="alert alert-danger small">
                                    {{ $errors->first('code') ?? $errors->first('reward') }}
                                </div>
                            @endif

                            @if (session('code-created'))
                                <div class="alert alert-success small">{{ session('code-created') }}</div>
                            @endif

                            @if (!$rewardOptionsAvailable)
                                <p class="small text-secondary mb-0 text-center">No hay recompensas disponibles para asignar.</p>
                            @else
                                <form method="POST" action="{{ route('admin.codigos.store') }}" class="d-grid gap-2">
                                    @csrf
                                    <div>
                                        <label for="reward_code" class="form-label">Código</label>
                                        <input id="reward_code" name="code" type="text" class="form-control form-control-sm text-uppercase" maxlength="64" value="{{ old('code') }}" required>
                                    </div>
                                    <div>
                                        <label for="reward_select" class="form-label">Recompensa</label>
                                        <select id="reward_select" name="reward" class="form-select form-select-sm" required>
                                            <option value="">Selecciona una recompensa</option>
                                            @foreach ($rewardOptionGroups as $group)
                                                <optgroup label="{{ $group['label'] }}">
                                                    @foreach ($group['options'] as $option)
                                                        <option value="{{ $option['value'] }}" @selected(old('reward') === $option['value'])>
                                                            {{ $option['label'] }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">Crear código</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column home-panel hx-min-0">
                <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden hx-card-bg">
                    <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Tienda semanal</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-3 overflow-hidden">
                        <p class="mb-2">Configura la montura destacada de la semana.</p>
                        <a href="{{ route('admin.tienda') }}" class="btn btn-outline-light btn-sm">Ir a tienda semanal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
