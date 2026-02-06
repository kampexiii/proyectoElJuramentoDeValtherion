@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100 chat-viewport game-viewport" id="chat-root"
    data-room-id="{{ $room->id }}"
    data-post-url="{{ route('game.chat.store', $room) }}"
    data-user-name="{{ auth()->user()->name ?? 'Usuario' }}">
    <div class="row h-100">
        <div class="col-12 h-100">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm h-100">
                <div class="card-header border-secondary bg-dark text-center py-2">Chat Global</div>
                <div class="card-body d-flex flex-column gap-2 h-100 overflow-hidden">
                    <div id="chat-messages" class="flex-grow-1 overflow-auto border border-secondary rounded p-2 bg-dark">
                        @forelse ($messages as $msg)
                            <div class="mb-2" data-message-id="{{ $msg->id }}">
                                <div class="d-flex justify-content-between small text-secondary">
                                    <span class="fw-semibold text-white">{{ $msg->user->name ?? 'Usuario' }}</span>
                                    <span>{{ $msg->created_at?->format('H:i') }}</span>
                                </div>
                                <div class="small">{{ $msg->message }}</div>
                            </div>
                        @empty
                            <div class="text-secondary small">Aún no hay mensajes. Sé el primero en escribir.</div>
                        @endforelse
                    </div>

                    <form id="chat-form" class="d-flex gap-2">
                        @csrf
                        <input id="chat-message" name="message" type="text" class="form-control" maxlength="500" placeholder="Escribe un mensaje..." autocomplete="off" required>
                        <button id="chat-submit" class="btn btn-primary" type="submit">Enviar</button>
                    </form>
                    <div class="small text-secondary">Sin scroll de página: solo el panel de mensajes hace scroll interno.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
