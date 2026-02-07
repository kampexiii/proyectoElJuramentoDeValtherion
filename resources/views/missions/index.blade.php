@extends('layouts.game.app')

@section('body-class', 'screen-shell view-missions layout-shell')
@section('main-class', 'layout-main')
@section('content')
<div class="view-missions w-100 h-100 d-flex align-items-center justify-content-center">
    <section class="missions-panel card bg-zinc-900 border-secondary text-white shadow-sm w-100 p-3 p-md-4">
        <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Misiones publicadas</h1>
                    <div class="small text-secondary">Elige una mision y comienza tu run.</div>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success small mb-0">{{ session('status') }}</div>
            @endif

            @if ($activeRun)
                <div class="alert alert-info small mb-0">
                    Tienes una run activa en "{{ $activeRun->mission?->title ?? 'Mision' }}".
                    <a href="{{ route('game.missions.run', $activeRun) }}" class="alert-link">Continuar</a>
                </div>
            @endif

            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-2">
                    @if ($missions->isEmpty())
                        <div class="small text-secondary">No hay misiones publicadas.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th>Titulo</th>
                                        <th>Boss final</th>
                                        <th>Repeatable</th>
                                        <th>Puntos base</th>
                                        <th class="text-end">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missions as $mission)
                                        <tr>
                                            <td>{{ $mission->title }}</td>
                                            <td>{{ $mission->finalBoss?->name ?? '-' }}</td>
                                            <td>{{ $mission->repeatable ? 'Si' : 'No' }}</td>
                                            <td>{{ $mission->base_race_points }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-info btn-sm" href="{{ route('game.missions.show', $mission) }}">Ver</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
