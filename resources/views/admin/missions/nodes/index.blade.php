@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Nodos de mision</h1>
                    <div class="small text-secondary">{{ $mission->title }} ({{ $mission->slug }})</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('missions.nodes.create', $mission) }}" class="btn btn-outline-info btn-sm">Crear nodo</a>
                    <a href="{{ route('missions.edit', $mission) }}" class="btn btn-outline-light btn-sm">Volver a mision</a>
                </div>
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

        @foreach ($steps as $step)
            @php
                $nodes = $nodesByStep[$step] ?? collect();
            @endphp
            <div class="col-12">
                <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                    <div class="card-header border-secondary bg-dark d-flex justify-content-between align-items-center py-2">
                        <div>Paso {{ $step }}</div>
                        <div class="small text-secondary">{{ $nodes->count() }} nodos</div>
                    </div>
                    <div class="card-body p-2">
                        @if ($nodes->isEmpty())
                            <div class="small text-secondary">Sin nodos en este paso.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-dark table-sm align-middle mb-0 small">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Inicio</th>
                                            <th>Opciones</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nodes as $node)
                                            <tr>
                                                <td>{{ $node->title ?: 'Nodo #' . $node->id }}</td>
                                                <td>
                                                    @if ($node->is_start)
                                                        <span class="badge bg-info text-dark">Inicio</span>
                                                    @else
                                                        <span class="text-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $node->choices_count }}</td>
                                                <td class="text-end">
                                                    <a class="btn btn-outline-info btn-sm" href="{{ route('missions.nodes.edit', [$mission, $node]) }}">Editar</a>
                                                    <a class="btn btn-outline-light btn-sm" href="{{ route('missions.nodes.choices.index', [$mission, $node]) }}">Opciones</a>
                                                    <form method="POST" action="{{ route('missions.nodes.destroy', [$mission, $node]) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar este nodo?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Borrar</button>
                                                    </form>
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
        @endforeach
    </div>
</div>
@endsection
