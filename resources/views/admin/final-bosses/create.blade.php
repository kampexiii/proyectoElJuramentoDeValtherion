@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Crear boss final</h1>
                    <div class="small text-secondary">Completa los datos base del boss.</div>
                </div>
                <a href="{{ route('final-bosses.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
        </div>

        <div class="col-12">
            @include('admin.final-bosses._form', [
                'boss' => null,
                'action' => route('final-bosses.store'),
                'method' => 'POST',
                'submitLabel' => 'Crear boss',
            ])
        </div>
    </div>
</div>
@endsection
