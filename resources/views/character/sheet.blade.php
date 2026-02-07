@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Ficha de personaje</h1>
                    <div class="small text-secondary">{{ $character->name }} · Nivel {{ $breakdown['level'] }}</div>
                </div>
                <a href="{{ route('game.perfil') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
        </div>

        @if (!empty($breakdown['base_out_of_range']))
            <div class="col-12">
                <div class="alert alert-warning small mb-0">
                    Base fuera de rango (0..12): {{ implode(', ', $breakdown['base_out_of_range']) }}. Revisar.
                </div>
            </div>
        @endif

        <div class="col-12 col-lg-4">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-2">Stats base (0..12, sin nivel)</div>
                <div class="card-body p-2">
                    <div class="small text-secondary">HP: {{ $breakdown['base_stats']['hp'] }}</div>
                    <div class="small text-secondary">Attack: {{ $breakdown['base_stats']['attack'] }}</div>
                    <div class="small text-secondary">Defense: {{ $breakdown['base_stats']['defense'] }}</div>
                    <div class="small text-secondary">Speed: {{ $breakdown['base_stats']['speed'] }}</div>
                    <div class="small text-secondary">Magic: {{ $breakdown['base_stats']['magic'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-2">Multiplicadores (segun nivel)</div>
                <div class="card-body p-2">
                    <div class="small text-secondary">HP: x{{ number_format($breakdown['multipliers_por_stat']['hp'], 2) }}</div>
                    <div class="small text-secondary">Attack: x{{ number_format($breakdown['multipliers_por_stat']['attack'], 2) }}</div>
                    <div class="small text-secondary">Defense: x{{ number_format($breakdown['multipliers_por_stat']['defense'], 2) }}</div>
                    <div class="small text-secondary">Speed: x{{ number_format($breakdown['multipliers_por_stat']['speed'], 2) }}</div>
                    <div class="small text-secondary">Magic: x{{ number_format($breakdown['multipliers_por_stat']['magic'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-2">Stats totales (nivel + equipo)</div>
                <div class="card-body p-2">
                    <div class="small text-secondary">HP: {{ $breakdown['total_stats']['hp'] }}</div>
                    <div class="small text-secondary">Attack: {{ $breakdown['total_stats']['attack'] }}</div>
                    <div class="small text-secondary">Defense: {{ $breakdown['total_stats']['defense'] }}</div>
                    <div class="small text-secondary">Speed: {{ $breakdown['total_stats']['speed'] }}</div>
                    <div class="small text-secondary">Magic: {{ $breakdown['total_stats']['magic'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-2">Desglose por stat</div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Stat</th>
                                    <th>Base</th>
                                    <th>Escalado</th>
                                    <th>Bonus equipo</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['hp', 'attack', 'defense', 'speed', 'magic'] as $stat)
                                    <tr>
                                        <td>{{ strtoupper($stat) }}</td>
                                        <td>{{ $breakdown['base_stats'][$stat] }}</td>
                                        <td>{{ $breakdown['scaled_stats'][$stat] }}</td>
                                        <td>{{ $breakdown['equipment_bonus'][$stat] }}</td>
                                        <td>{{ $breakdown['total_stats'][$stat] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
