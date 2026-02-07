@php
    $statusValue = $mission->status->value ?? $mission->status;
    $isPublished = $statusValue === 'published';
@endphp

<div class="card bg-zinc-900 border-secondary text-white shadow-sm">
    <div class="card-header border-secondary bg-dark text-center py-2">Publicacion</div>
    <div class="card-body p-3">
        <div class="small text-secondary mb-2">Se valida el grafo completo antes de publicar.</div>
        <div class="d-flex flex-column flex-md-row gap-2">
            <div class="small">Estado actual: <strong>{{ $statusValue }}</strong></div>
        </div>
        <form method="POST" action="{{ route('missions.publish', $mission) }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-outline-success btn-sm" @disabled($isPublished)>
                {{ $isPublished ? 'Mision publicada' : 'Publicar mision' }}
            </button>
        </form>
    </div>
</div>
