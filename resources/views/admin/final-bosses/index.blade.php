@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Bosses finales</h1>
                    <div class="small text-secondary">Gestiona los bosses finales y sus stats base.</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('final-bosses.create') }}" class="btn btn-outline-info btn-sm">Crear boss</a>
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
                    @if ($bosses->isEmpty())
                        <div class="small text-secondary">No hay bosses registrados.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Slug</th>
                                        <th>HP</th>
                                        <th>Daño</th>
                                        <th>Defensa</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bosses as $boss)
                                        @php
                                            $stats = $boss->base_stats_json ?? [];
                                        @endphp
                                        <tr>
                                            <td>{{ $boss->name }}</td>
                                            <td>{{ $boss->slug }}</td>
                                            <td>{{ $stats['hp'] ?? '-' }}</td>
                                            <td>{{ $stats['damage'] ?? '-' }}</td>
                                            <td>{{ $stats['defense'] ?? '-' }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-info btn-sm" href="{{ route('final-bosses.edit', $boss) }}">Editar</a>
                                                <form method="POST" action="{{ route('final-bosses.destroy', $boss) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar este boss?');">
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
                            {{ $bosses->links('pagination::simple-bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
