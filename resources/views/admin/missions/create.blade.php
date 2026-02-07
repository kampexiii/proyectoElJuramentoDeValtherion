@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Crear mision</h1>
                    <div class="small text-secondary">Configura la cabecera y recompensas.</div>
                </div>
                <a href="{{ route('missions.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
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
            <form method="POST" action="{{ route('missions.store') }}" class="d-grid gap-3">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-xl-6">
                        @include('admin.missions._form', [
                            'mission' => null,
                            'bosses' => $bosses,
                            'statusOptions' => $statusOptions,
                        ])
                    </div>
                    <div class="col-12 col-xl-6">
                        @include('admin.missions._rewards', [
                            'mission' => null,
                            'reward' => null,
                            'items' => $items,
                            'hasItemsTable' => $hasItemsTable,
                        ])
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info btn-sm">Crear mision</button>
            </form>
        </div>
    </div>
</div>
@endsection
