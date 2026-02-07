@extends('layouts.game.app')

@section('content')
@php
    $isBossPending = $run->status === \App\Enums\MissionRunStatus::BossPending;
    $canAbandonNormal = $run->status === \App\Enums\MissionRunStatus::Active;
    $canRetreatPartial = in_array($run->status, [\App\Enums\MissionRunStatus::Active, \App\Enums\MissionRunStatus::BossPending], true);
@endphp
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">{{ $mission->title }}</h1>
                    <div class="small text-secondary">Run #{{ $run->id }} · Paso {{ $run->current_step_index }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('game.missions.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
                    @if ($canAbandonNormal)
                        <form method="POST" action="{{ route('game.missions.abandon', $run) }}" onsubmit="return confirm('¿Seguro que quieres abandonar la mision?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Abandonar</button>
                        </form>
                    @endif
                    @if ($canRetreatPartial && !$isBossPending)
                        <div class="d-flex flex-column align-items-start gap-1">
                            <form method="POST" action="{{ route('game.missions.abandon', $run) }}" onsubmit="return confirm('¿Seguro que quieres retirarte? Recibes 10% de XP.');">
                                @csrf
                                <input type="hidden" name="partial" value="1">
                                <button type="submit" class="btn btn-outline-warning btn-sm">Retirarse (10% XP)</button>
                            </form>
                            <div class="small text-warning">Solo XP (sin oro, sin objetos, sin puntos de raza).</div>
                        </div>
                    @endif
                </div>
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
                    @if ($isBossPending)
                        <div class="d-flex flex-column gap-2">
                            <div class="alert alert-info small mb-0">Has llegado al boss final. Elige tu siguiente accion.</div>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-outline-info btn-sm" href="{{ route('game.missions.boss.show', $run) }}">Enfrentar boss</a>
                                <form method="POST" action="{{ route('game.missions.abandon', $run) }}" onsubmit="return confirm('¿Seguro que quieres retirarte? Recibes 10% de XP.');">
                                    @csrf
                                    <input type="hidden" name="partial" value="1">
                                    <button type="submit" class="btn btn-outline-warning btn-sm">Retirarse (10% XP)</button>
                                </form>
                            </div>
                            <div class="small text-warning">Retirarse solo da XP (10%). Sin oro, sin objetos, sin puntos de raza.</div>
                        </div>
                    @elseif (!$node)
                        <div class="small text-secondary">No hay nodo activo.</div>
                    @else
                        <div class="small text-secondary mb-2">{{ $node->title ?: 'Nodo actual' }}</div>
                        <div class="mb-3">{{ $node->body_text }}</div>

                        <form method="POST" action="{{ route('game.missions.choose', $run) }}" class="d-grid gap-2">
                            @csrf
                            @foreach ($choices as $choice)
                                <label class="border border-secondary rounded p-2 d-flex gap-2 align-items-start">
                                    <input type="radio" name="choice_id" value="{{ $choice->id }}" class="form-check-input mt-1" required>
                                    <div>
                                        <div class="fw-semibold">{{ $choice->choice_text }}</div>
                                        @if ($choice->outcome_text)
                                            <div class="small text-secondary">{{ $choice->outcome_text }}</div>
                                        @endif
                                        <div class="small text-secondary">Dificultad: {{ $choice->difficulty_points }}</div>
                                    </div>
                                </label>
                            @endforeach
                            <button type="submit" class="btn btn-outline-info btn-sm">Elegir opcion</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="small text-secondary">Danger score: {{ $run->danger_score }}</div>
                    <div class="small text-secondary">Wound stacks: {{ $run->wound_stacks }}</div>
                    <div class="small text-secondary">Estado: {{ $run->status->value ?? $run->status }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
