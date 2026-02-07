@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Editar mision</h1>
                    <div class="small text-secondary">Actualiza cabecera, rewards y enlaces al builder.</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('missions.nodes.index', $mission) }}" class="btn btn-outline-info btn-sm">Gestionar Nodos (Tramos)</a>
                    <a href="{{ route('missions.nodes.index', $mission) }}" class="btn btn-outline-light btn-sm">Gestionar Choices (Opciones)</a>
                    <a href="{{ route('missions.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
                </div>
            </div>
        </div>

        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success small mb-2">{{ session('status') }}</div>
            @endif
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

        <div class="col-12">
            <form method="POST" action="{{ route('missions.update', $mission) }}" class="d-grid gap-3">
                @csrf
                @method('PUT')
                @include('admin.missions._form', [
                    'mission' => $mission,
                    'bosses' => $bosses,
                    'statusOptions' => $statusOptions,
                ])
                @include('admin.missions._rewards', [
                    'mission' => $mission,
                    'reward' => $mission->reward,
                    'items' => $items,
                    'hasItemsTable' => $hasItemsTable,
                ])
                <button type="submit" class="btn btn-outline-info btn-sm">Actualizar mision</button>
            </form>
        </div>
    </div>
</div>
@endsection
