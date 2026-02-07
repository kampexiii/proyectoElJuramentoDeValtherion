@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Opciones del nodo</h1>
                    <div class="small text-secondary">Paso {{ $stepIndex }} · {{ $mission->title }} · {{ $node->title ?: 'Nodo #' . $node->id }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('missions.nodes.choices.create', [$mission, $node]) }}" class="btn btn-outline-info btn-sm" @disabled($choices->count() >= 4)>Crear opcion</a>
                    <a href="{{ route('missions.nodes.index', $mission) }}" class="btn btn-outline-light btn-sm">Volver a nodos</a>
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

        <div class="col-12">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-secondary">{{ $choices->count() }} opciones (min 3, max 4)</div>
                        @if ($choices->count() < 3)
                            <div class="small text-warning">Faltan opciones para cumplir el minimo.</div>
                        @endif
                    </div>
                    @if ($choices->isEmpty())
                        <div class="small text-secondary">No hay opciones registradas.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th>Orden</th>
                                        <th>Opcion</th>
                                        <th>Dificultad</th>
                                        <th>Destino</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($choices as $choice)
                                        <tr>
                                            <td>{{ $choice->order }}</td>
                                            <td>{{ $choice->choice_text }}</td>
                                            <td>{{ $choice->difficulty_points }}</td>
                                            <td>
                                                @if ($choice->goes_to_boss)
                                                    Boss final
                                                @elseif ($choice->nextNode)
                                                    Paso {{ $choice->nextNode->step_index }} · {{ $choice->nextNode->title ?: 'Nodo #' . $choice->nextNode->id }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-info btn-sm" href="{{ route('missions.nodes.choices.edit', [$mission, $node, $choice]) }}">Editar</a>
                                                <form method="POST" action="{{ route('missions.nodes.choices.destroy', [$mission, $node, $choice]) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar esta opcion?');">
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
    </div>
</div>
@endsection
