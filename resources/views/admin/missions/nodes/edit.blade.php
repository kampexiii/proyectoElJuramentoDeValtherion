@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Editar nodo</h1>
                    <div class="small text-secondary">Mision: {{ $mission->title }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('missions.nodes.choices.index', [$mission, $node]) }}" class="btn btn-outline-info btn-sm">Gestionar opciones</a>
                    <a href="{{ route('missions.nodes.index', $mission) }}" class="btn btn-outline-light btn-sm">Volver</a>
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

        <div class="col-12">
            <form method="POST" action="{{ route('missions.nodes.update', [$mission, $node]) }}" class="d-grid gap-3">
                @csrf
                @method('PUT')
                <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                    <div class="card-body p-3 d-grid gap-2">
                        <div>
                            <label class="form-label" for="node_step">Paso</label>
                            <select id="node_step" name="step_index" class="form-select form-select-sm" required>
                                @foreach ($steps as $step)
                                    <option value="{{ $step }}" @selected((int) old('step_index', $node->step_index) === $step)>{{ $step }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check">
                            <input id="node_start" name="is_start" type="checkbox" value="1" class="form-check-input" @checked(old('is_start', $node->is_start))>
                            <label class="form-check-label" for="node_start">Marcar como inicio (solo paso 1)</label>
                        </div>

                        <div>
                            <label class="form-label" for="node_title">Titulo</label>
                            <input id="node_title" name="title" class="form-control form-control-sm" value="{{ old('title', $node->title) }}">
                        </div>

                        <div>
                            <label class="form-label" for="node_body">Texto</label>
                            <textarea id="node_body" name="body_text" rows="4" class="form-control form-control-sm" required>{{ old('body_text', $node->body_text) }}</textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info btn-sm">Actualizar nodo</button>
            </form>
        </div>
    </div>
</div>
@endsection
