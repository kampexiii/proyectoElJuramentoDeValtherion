@extends('layouts.game.app')

@section('content')
<div class="container-fluid perfil-viewport">
    <div class="perfil-header">
        <h1 class="h4 mb-1">Perfil</h1>
        <p class="small text-secondary mb-0">Gestiona tu cuenta y tu personaje.</p>
    </div>

    <div class="row g-2 perfil-row">
        <div class="col-12 col-xl-6 perfil-col d-flex">
            <section class="card bg-zinc-900 border-secondary text-white shadow-sm perfil-panel" aria-labelledby="perfil-cuenta">
                <div class="card-header border-secondary bg-dark text-center py-1 perfil-header">
                    <h2 id="perfil-cuenta" class="h6 mb-0">Cuenta</h2>
                </div>
                <div class="card-body p-2 perfil-panel-body">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success small">Perfil actualizado.</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" class="d-grid gap-2" id="profile-form" data-availability-url="{{ route('game.perfil.disponibilidad') }}">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="profile_name" class="form-label">Nombre de usuario</label>
                            <input
                                id="profile_name"
                                name="name"
                                type="text"
                                class="form-control form-control-sm"
                                value="{{ old('name', $user->name ?? '') }}"
                                autocomplete="username"
                                required
                                data-current="{{ $user->name ?? '' }}"
                            >
                            @error('name')
                                <div class="small text-danger">{{ $message }}</div>
                            @enderror
                            <div id="name-availability" class="small" aria-live="polite"></div>
                        </div>

                        <div>
                            <label for="profile_email" class="form-label">Correo</label>
                            <input
                                id="profile_email"
                                name="email"
                                type="email"
                                class="form-control form-control-sm"
                                value="{{ old('email', $user->email ?? '') }}"
                                autocomplete="email"
                                required
                                data-current="{{ $user->email ?? '' }}"
                            >
                            @error('email')
                                <div class="small text-danger">{{ $message }}</div>
                            @enderror
                            <div id="email-availability" class="small" aria-live="polite"></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">Guardar</button>
                    </form>

                    <div class="perfil-verify small text-secondary">
                        @if ($mustVerifyEmail && $user && !$user->hasVerifiedEmail())
                            Tu correo no está verificado.
                        @else
                            Correo verificado.
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6 perfil-col d-flex">
            <section class="card bg-zinc-900 border-secondary text-white shadow-sm perfil-panel" aria-labelledby="perfil-juego">
                <div class="card-header border-secondary bg-dark text-center py-1 perfil-header">
                    <h2 id="perfil-juego" class="h6 mb-0">Juego</h2>
                </div>
                <div class="card-body p-2 perfil-panel-body">
                    <div class="perfil-section-title small text-secondary">Tu personaje</div>
                    @if ($characterSummary)
                        <div class="list-group list-group-flush">
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 d-flex justify-content-between">
                                <span>Nombre</span>
                                <span class="text-truncate">{{ $characterSummary['name'] }}</span>
                            </div>
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 d-flex justify-content-between">
                                <span>Raza</span>
                                <span class="text-truncate">{{ $characterSummary['race'] }}</span>
                            </div>
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 d-flex justify-content-between">
                                <span>Nivel</span>
                                <span class="text-truncate">{{ $characterSummary['level'] }}</span>
                            </div>
                        </div>
                    @else
                        <div class="perfil-cta small text-secondary">Aún no tienes personaje.</div>
                        @if (Route::has('game.personaje.create'))
                            <a href="{{ route('game.personaje.create') }}" class="btn btn-outline-light btn-sm w-100">Crear personaje</a>
                        @endif
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
