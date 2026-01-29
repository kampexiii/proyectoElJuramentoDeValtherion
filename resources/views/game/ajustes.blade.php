@extends('layouts.game.app')

@section('content')
<div class="container-fluid ajustes-viewport">
    <div class="row g-3 ajustes-row">
        <div class="col-12 ajustes-col d-flex">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm ajustes-panel">
                <div class="card-header border-secondary bg-dark text-center py-1 ajustes-header">
                    <span class="ajustes-title"><i class="bi bi-sliders me-2"></i>Preferencias del juego</span>
                </div>
                <div class="card-body p-2 ajustes-panel-body">
                    <form id="game-settings-form" class="d-grid gap-2">
                        <div class="ajustes-switch">
                            <div>
                                <label class="form-check-label fw-semibold" for="compact_mode">Modo compacto</label>
                                <div class="small text-secondary">Reduce el espacio entre bloques.</div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="compact_mode">
                            </div>
                        </div>
                        <div class="ajustes-switch">
                            <div>
                                <label class="form-check-label fw-semibold" for="reduce_motion">Reducir animaciones</label>
                                <div class="small text-secondary">Más suave y estable.</div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="reduce_motion">
                            </div>
                        </div>
                        <div class="ajustes-switch">
                            <div>
                                <label class="form-check-label fw-semibold vth-sound-indicator" for="sound_enabled">Sonido activado</label>
                                <div class="small text-secondary">Solo preferencia visual.</div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="sound_enabled">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Guardar preferencias</button>
                    </form>
                    <div id="settings-status" class="alert alert-success small d-none mb-0">Preferencias guardadas.</div>
                </div>
            </div>
        </div>

        <div class="col-12 ajustes-col d-flex">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm ajustes-panel">
                <div class="card-header border-secondary bg-dark text-center py-1 ajustes-header">
                    <span class="ajustes-title"><i class="bi bi-shield-lock me-2"></i>Seguridad</span>
                </div>
                <div class="card-body p-2 ajustes-panel-body">
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

                    <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-2 ajustes-section">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input id="current_password" name="current_password" type="password" class="form-control form-control-sm" autocomplete="current-password" required>
                        </div>

                        <div>
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input id="password" name="password" type="password" class="form-control form-control-sm" autocomplete="new-password" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">Repetir nueva contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-control-sm" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-warning btn-sm w-100">Cambiar contraseña</button>
                    </form>

                    <div class="ajustes-section">
                        @if ($errors->has('code'))
                            <div class="alert alert-danger small mb-2">{{ $errors->first('code') }}</div>
                        @endif

                        @if (session('code-status'))
                            <div class="alert alert-success small mb-2">{{ session('code-status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('game.ajustes.codigo') }}" class="d-grid gap-2 ajustes-code-form">
                            @csrf
                            <div>
                                <label for="reward_code" class="form-label">Código de recompensa</label>
                                <div class="input-group input-group-sm">
                                    <input id="reward_code" name="code" type="text" class="form-control text-uppercase" maxlength="64" autocomplete="off" required>
                                    <button type="submit" class="btn btn-outline-info">Canjear</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto ajustes-section">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
