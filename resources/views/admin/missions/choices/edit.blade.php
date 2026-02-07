@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-3 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Editar opcion</h1>
                    <div class="small text-secondary">Paso {{ $stepIndex }} · {{ $mission->title }}</div>
                </div>
                <a href="{{ route('missions.nodes.choices.index', [$mission, $node]) }}" class="btn btn-outline-light btn-sm">Volver</a>
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
            <form method="POST" action="{{ route('missions.nodes.choices.update', [$mission, $node, $choice]) }}" class="d-grid gap-3">
                @csrf
                @method('PUT')
                <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                    <div class="card-body p-3 d-grid gap-2">
                        <div>
                            <label class="form-label" for="choice_text">Texto de la opcion</label>
                            <input id="choice_text" name="choice_text" class="form-control form-control-sm" value="{{ old('choice_text', $choice->choice_text) }}" required>
                        </div>

                        <div>
                            <label class="form-label" for="outcome_text">Texto de resultado</label>
                            <textarea id="outcome_text" name="outcome_text" rows="3" class="form-control form-control-sm">{{ old('outcome_text', $choice->outcome_text) }}</textarea>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="difficulty_points">Dificultad</label>
                                <select id="difficulty_points" name="difficulty_points" class="form-select form-select-sm" required>
                                    @for ($i = 0; $i <= 3; $i++)
                                        <option value="{{ $i }}" @selected((int) old('difficulty_points', $choice->difficulty_points) === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="choice_order">Orden</label>
                                <select id="choice_order" name="order" class="form-select form-select-sm" required>
                                    @foreach ($orderOptions as $order)
                                        <option value="{{ $order }}" @selected((int) old('order', $choice->order) === $order)>{{ $order }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($stepIndex < 6)
                            <div>
                                <label class="form-label" for="next_node_id">Siguiente nodo (paso {{ $stepIndex + 1 }})</label>
                                <select id="next_node_id" name="next_node_id" class="form-select form-select-sm" required>
                                    <option value="">Selecciona un nodo</option>
                                    @foreach ($nextNodes as $nextNode)
                                        <option value="{{ $nextNode->id }}" @selected((int) old('next_node_id', $choice->next_node_id) === $nextNode->id)>
                                            {{ $nextNode->title ?: 'Nodo #' . $nextNode->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="goes_to_boss" value="0">
                        @else
                            <div class="alert alert-info small mb-0">En el paso 6 todas las opciones van al boss final.</div>
                            <input type="hidden" name="goes_to_boss" value="1">
                        @endif

                        <div>
                            <label class="form-label" for="effects_json_raw">Effects JSON</label>
                            <textarea id="effects_json_raw" name="effects_json_raw" rows="4" class="form-control form-control-sm">{{ old('effects_json_raw', $choice->effects_json ? json_encode($choice->effects_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info btn-sm">Actualizar opcion</button>
            </form>
        </div>
    </div>
</div>
@endsection
