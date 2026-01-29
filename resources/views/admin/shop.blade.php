@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12 col-lg-6 d-flex">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Montura semanal</div>
                <div class="card-body d-flex flex-column justify-content-center p-3 overflow-hidden">
                    <div class="small text-secondary mb-2">Configuración para {{ $weekLabel }}</div>

                    @if (session('mount-status'))
                        <div class="alert alert-success small">{{ session('mount-status') }}</div>
                    @endif

                    @if ($errors->has('item_id'))
                        <div class="alert alert-danger small">{{ $errors->first('item_id') }}</div>
                    @endif

                    @if (!$hasShopTable)
                        <div class="alert alert-warning small mb-0">Falta ejecutar migraciones para la tienda semanal.</div>
                    @elseif ($mounts->isEmpty())
                        <p class="small text-secondary mb-0">No hay monturas cargadas en la tabla de items.</p>
                    @else
                        <form method="POST" action="{{ route('admin.tienda.montura') }}" class="d-grid gap-2">
                            @csrf
                            <div>
                                <label for="mount_item_id" class="form-label">Montura en venta</label>
                                <select id="mount_item_id" name="item_id" class="form-select form-select-sm" required>
                                    <option value="">Selecciona una montura</option>
                                    @foreach ($mounts as $mount)
                                        <option value="{{ $mount->id }}" @selected(old('item_id') == $mount->id)>
                                            {{ $mount->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Guardar montura</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 d-flex">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm overflow-hidden">
                <div class="card-header border-secondary bg-dark text-center py-2 flex-shrink-0">Montura actual</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-3 overflow-hidden">
                    @if ($currentMount)
                        <div class="h6 mb-1">{{ $currentMount->name }}</div>
                        <div class="small text-secondary mb-0">Montura asignada para esta semana.</div>
                    @else
                        <div class="small text-secondary mb-0">Aún no hay montura seleccionada.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
