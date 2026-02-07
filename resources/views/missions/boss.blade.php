@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Boss final</h1>
                    <div class="small text-secondary">{{ $mission->title }}</div>
                </div>
                <a href="{{ route('game.missions.run', $run) }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
        </div>

        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success small mb-0">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small mb-0">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-7">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="small text-secondary mb-1">Tier</div>
                    <div class="mb-2">{{ $tier['label'] ?? $tier['key'] }}</div>

                    <div class="small text-secondary mb-1">Multiplicador boss</div>
                    <div class="mb-2">x{{ $combat['boss_multiplier'] ?? '-' }}</div>

                    <div class="small text-secondary mb-1">Debuff wounds</div>
                    <div class="mb-2">x{{ $combat['wound_multiplier'] ?? '-' }}</div>

                    <div class="small text-secondary mb-1">Power jugador</div>
                    <div class="mb-2">{{ $combat['character_power'] ?? '-' }}</div>

                    <div class="small text-secondary mb-1">Power boss</div>
                    <div class="mb-2">{{ $combat['boss_power'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-3 d-grid gap-2">
                    @if (!$combat['resolvable'])
                        <div class="small text-warning">{{ $combat['message'] }}</div>
                    @else
                        <div class="small text-secondary">Resultado esperado: {{ $combat['win'] ? 'Victoria' : 'Derrota' }}</div>
                        <form method="POST" action="{{ route('game.missions.boss.fight', $run) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">Pelear boss</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
