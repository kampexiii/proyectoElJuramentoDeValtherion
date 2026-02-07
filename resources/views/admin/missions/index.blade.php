@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Misiones</h1>
                    <div class="small text-secondary">Gestiona las misiones narrativas base.</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('missions.create') }}" class="btn btn-outline-info btn-sm">Crear mision</a>
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-light btn-sm">Volver al panel</a>
                </div>
            </div>
        </div>

        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success small mb-0">{{ session('status') }}</div>
            @endif
        </div>

        <div class="col-12">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-body p-2">
                    @if ($missions->isEmpty())
                        <div class="small text-secondary">No hay misiones registradas.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th>Titulo</th>
                                        <th>Slug</th>
                                        <th>Estado</th>
                                        <th>Repeatable</th>
                                        <th>Puntos base</th>
                                        <th>Boss final</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missions as $mission)
                                        <tr>
                                            <td>{{ $mission->title }}</td>
                                            <td>{{ $mission->slug }}</td>
                                            <td>{{ $mission->status->value ?? $mission->status }}</td>
                                            <td>{{ $mission->repeatable ? 'Si' : 'No' }}</td>
                                            <td>{{ $mission->base_race_points }}</td>
                                            <td>{{ $mission->finalBoss?->name ?? '-' }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-info btn-sm" href="{{ route('missions.edit', $mission) }}">Editar</a>
                                                <form method="POST" action="{{ route('missions.destroy', $mission) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar esta mision?');">
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

                        <div class="mt-2">
                            {{ $missions->links('pagination::simple-bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
