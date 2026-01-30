@extends('layouts.game.app')

@section('content')
@php
    $slots = [
        'helmet' => ['label' => 'Cascos', 'field' => 'helmet_item_id', 'empty' => 'Sin casco'],
        'armor' => ['label' => 'Armaduras', 'field' => 'armor_item_id', 'empty' => 'Sin armadura'],
        'weapon' => ['label' => 'Armas', 'field' => 'weapon_item_id', 'empty' => 'Sin arma'],
        'ring' => ['label' => 'Anillos', 'field' => 'ring_item_id', 'empty' => 'Sin anillo'],
        'amulet' => ['label' => 'Colgantes', 'field' => 'amulet_item_id', 'empty' => 'Sin colgante'],
        'mount' => ['label' => 'Montura', 'field' => 'mount_item_id', 'empty' => 'Sin montura'],
    ];
@endphp

<div class="equipamiento-viewport">
    <div class="equipamiento-header">
        <div>
            <h1 class="h5 mb-0">Armería</h1>
            <p class="small text-secondary mb-0">Elige el equipo que llevarás al combate.</p>
        </div>
        <button form="equipamiento-form" type="submit" class="btn btn-primary btn-sm">Guardar equipamiento</button>
    </div>

    @if ($errors->has('equipamiento'))
        <div class="alert alert-danger small mb-2">{{ $errors->first('equipamiento') }}</div>
    @endif

    @if (session('status'))
        <div class="alert alert-success small mb-2">{{ session('status') }}</div>
    @endif

    <form id="equipamiento-form" method="POST" action="{{ route('game.equipamiento.update') }}" class="equipamiento-grid">
        @csrf

        @foreach ($slots as $slot => $data)
            @if ($slot !== 'mount' || $showMount)
                @php
                    $items = $options[$slot] ?? collect();
                    $currentId = $current[$slot] ?? null;
                @endphp
                <div class="equipamiento-card">
                    <label for="{{ $data['field'] }}" class="form-label">{{ $data['label'] }}</label>
                    <select id="{{ $data['field'] }}" name="{{ $data['field'] }}" class="form-select form-select-sm">
                        <option value="">{{ $data['empty'] }}</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected($currentId == $item->id)>
                                {{ $item->name }}
                                {{-- Montura real eliminado: ahora todas son equipables --}}
                            </option>
                        @endforeach
                    </select>
                    @error($data['field'])
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                    @if ($items->isEmpty())
                        <div class="small text-secondary">No posees objetos para este hueco.</div>
                    @endif
                </div>
            @endif
        @endforeach
    </form>
</div>
@endsection
