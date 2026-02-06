<x-auth-layout>
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        @if ($errors->has('cookie'))
            <div class="mb-4 text-sm text-red-500">
                {{ $errors->first('cookie') }}
            </div>
        @endif

        <div id="register-hints" class="mb-4 text-sm text-red-500" role="status" aria-live="polite"></div>

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ej: Valtherion" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                title="El correo debe tener formato usuario@dominio.com"
                placeholder="Ej: usuario@dominio.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimo 8 caracteres" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar Contraseña" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Repite la contrasena" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="inline-flex items-start gap-2 text-sm text-zinc-400">
                <input name="accept_cookies" type="checkbox" class="rounded border-zinc-700 bg-zinc-950 text-zinc-600 shadow-sm focus:ring-zinc-500" required>
                <span>
                    Acepto el uso de cookies.
                    <a href="{{ route('legal.cookies') }}" class="underline text-zinc-300 hover:text-white">Ver cookies</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('accept_cookies')" class="mt-2" />
        </div>

        <div class="mt-2">
            <label class="inline-flex items-start gap-2 text-sm text-zinc-400">
                <input name="accept_terms" type="checkbox" class="rounded border-zinc-700 bg-zinc-950 text-zinc-600 shadow-sm focus:ring-zinc-500" required>
                <span>
                    Acepto los terminos y condiciones.
                    <a href="{{ route('legal.terms') }}" class="underline text-zinc-300 hover:text-white">Ver terminos</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4 auth-actions auth-actions--between">
            <a class="underline text-sm text-zinc-400 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500" href="{{ route('login') }}">
                ¿Ya estás registrado?
            </a>

            <x-primary-button class="ms-4 auth-btn">
                Registrarse
            </x-primary-button>
        </div>
    </form>
</x-auth-layout>
