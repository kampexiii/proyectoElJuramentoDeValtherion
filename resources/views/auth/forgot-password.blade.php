<x-auth-layout>
    <div class="mb-4 text-sm text-zinc-400">
        ¿Has olvidado tu contraseña? No hay problema. Dinós tu dirección de correo electrónico y te enviaremos un enlace para restablecerla.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4 auth-actions">
            <x-primary-button class="auth-btn">
                Enviar enlace de restablecimiento
            </x-primary-button>
        </div>
    </form>
</x-auth-layout>
