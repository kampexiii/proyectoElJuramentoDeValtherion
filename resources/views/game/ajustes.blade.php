@extends('layouts.game.app')

@section('content')
<div class="container-fluid ajustes-viewport">
    <div class="row g-3 ajustes-row">
        <div class="col-12 col-xl-6 ajustes-col d-flex">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm ajustes-panel">
                <div class="card-header border-secondary bg-dark text-center py-2">Preferencias del juego</div>
                <div class="card-body p-3 ajustes-panel-body">
                    <form id="game-settings-form" class="d-grid gap-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="compact_mode">
                            <label class="form-check-label" for="compact_mode">Modo compacto</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="reduce_motion">
                            <label class="form-check-label" for="reduce_motion">Reducir animaciones</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="sound_enabled">
                            <label class="form-check-label vth-sound-indicator" for="sound_enabled">Sonido activado</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar preferencias</button>
                    </form>
                    <div id="settings-status" class="alert alert-success small d-none mb-0">Preferencias guardadas.</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6 ajustes-col d-flex">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm ajustes-panel">
                <div class="card-header border-secondary bg-dark text-center py-2">Seguridad</div>
                <div class="card-body p-3 ajustes-panel-body">
                    @if ($errors->updatePassword->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success small">Contraseña actualizada.</div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-2">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                        </div>

                        <div>
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">Repetir nueva contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Cambiar contraseña</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-light w-100">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
