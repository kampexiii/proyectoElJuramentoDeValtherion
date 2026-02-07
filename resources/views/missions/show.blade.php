@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">{{ $mission->title }}</h1>
                    <div class="small text-secondary">Boss final: {{ $mission->finalBoss?->name ?? 'Sin asignar' }}</div>
                </div>
                <a href="{{ route('game.missions.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
        </div>

        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-8">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="mb-3">
                        <div class="small text-secondary mb-1">Intro</div>
                        <div>{{ $mission->intro_text }}</div>
                    </div>
                    @if ($mission->context_text)
                        <div class="mb-3">
                            <div class="small text-secondary mb-1">Contexto</div>
                            <div>{{ $mission->context_text }}</div>
                        </div>
                    @endif
                    <div class="small text-secondary">Puntos base: {{ $mission->base_race_points }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-3 d-grid gap-2">
                    @if ($activeRun && $activeRun->mission_id !== $mission->id)
                        <div class="small text-warning">Ya tienes una run activa en otra mision.</div>
                        <a href="{{ route('game.missions.run', $activeRun) }}" class="btn btn-outline-info btn-sm">Continuar run activa</a>
                    @elseif ($activeRun && $activeRun->mission_id === $mission->id)
                        <div class="small text-info">Tienes una run activa en esta mision.</div>
                        <a href="{{ route('game.missions.run', $activeRun) }}" class="btn btn-outline-info btn-sm">Continuar</a>
                    @else
                        <form method="POST" action="{{ route('game.missions.start', $mission) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm w-100">Iniciar run</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
