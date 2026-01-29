<x-auth-layout>
    <div class="mb-4 text-center">
        <h1 class="h4 mb-1">Acceso de Administración</h1>
        <p class="small text-zinc-400 mb-0">Requiere credenciales de Alto Consejo</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4 auth-remember">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-zinc-700 bg-zinc-950 text-zinc-600 shadow-sm focus:ring-zinc-500" name="remember">
                <span class="ms-2 text-sm text-zinc-400">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 auth-actions">
            <x-primary-button class="ms-3 auth-btn">
                Entrar al Panel
            </x-primary-button>
        </div>
    </form>
</x-auth-layout>
