<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="El Juramento de Valtherion - Juego de rol estratégico ambientado en el mundo de Warhammer Fantasy. Crea tu personaje, lucha en misiones y domina el campo de batalla.">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CSS/JS zona logueada -->
    @vite(['resources/css/game/app.css', 'resources/js/game/app.js'])
</head>
<body class="antialiased auth-shell">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 auth-wrap">
        <div class="auth-logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-900 shadow-md overflow-hidden sm:rounded-lg border border-zinc-800 auth-card">
            {{ $slot }}
        </div>
    </div>

    <div id="cookie-banner" class="fixed bottom-3 left-3 right-3 sm:left-auto sm:right-6 sm:max-w-sm bg-zinc-900 border border-zinc-800 text-white text-sm p-4 rounded-lg shadow-lg" style="display: none;">
        <div class="mb-3">Usamos cookies para que el registro y el acceso funcionen correctamente.</div>
        <div class="flex gap-2 justify-end">
            <button id="cookie-decline" type="button" class="btn btn-outline-light btn-sm">Rechazar</button>
            <button id="cookie-accept" type="button" class="btn btn-primary btn-sm">Aceptar</button>
        </div>
    </div>

    <script>
        (function () {
            const forms = document.querySelectorAll('form');
            const consentCookie = 'vth_cookie_consent';
            const banner = document.getElementById('cookie-banner');
            const acceptBtn = document.getElementById('cookie-accept');
            const declineBtn = document.getElementById('cookie-decline');
            const registerHints = document.getElementById('register-hints');

            const hasConsent = () => document.cookie.split('; ').some((row) => row.startsWith(consentCookie + '=accepted'));

            const setConsent = () => {
                const maxAge = 60 * 60 * 24 * 365;
                let cookie = consentCookie + '=accepted; path=/; max-age=' + maxAge + '; samesite=lax';
                if (window.location.protocol === 'https:') {
                    cookie += '; secure';
                }
                document.cookie = cookie;
            };

            if (banner && !hasConsent()) {
                banner.style.display = 'block';
            }

            if (acceptBtn) {
                acceptBtn.addEventListener('click', () => {
                    setConsent();
                    if (banner) {
                        banner.style.display = 'none';
                    }
                });
            }

            if (declineBtn) {
                declineBtn.addEventListener('click', () => {
                    if (banner) {
                        banner.style.display = 'none';
                    }
                });
            }

            const emailError = (value) => {
                const trimmed = value.trim();
                if (!trimmed) {
                    return 'Completa este campo.';
                }

                if (!trimmed.includes('@')) {
                    return 'Falta el simbolo @ en el correo.';
                }

                const atIndex = trimmed.lastIndexOf('@');
                const local = trimmed.slice(0, atIndex).trim();
                const domain = trimmed.slice(atIndex + 1).trim();

                if (!local) {
                    return 'Falta el usuario antes de la @.';
                }

                if (!domain) {
                    return 'Falta el dominio despues de la @.';
                }

                if (!domain.includes('.')) {
                    return 'Falta el punto en el dominio (ej: dominio.com).';
                }

                return '';
            };

            forms.forEach((form) => {
                const password = form.querySelector('input[name="password"]');
                const passwordConfirmation = form.querySelector('input[name="password_confirmation"]');
                const email = form.querySelector('input[name="email"]');

                const setHint = (message) => {
                    if (!registerHints) {
                        return;
                    }
                    registerHints.textContent = message || '';
                };

                form.querySelectorAll('input, select, textarea').forEach((field) => {
                    field.addEventListener('invalid', () => {
                        if (field.validity.valueMissing) {
                                if (field.name === 'accept_cookies') {
                                    field.setCustomValidity('Debes aceptar las cookies para continuar.');
                                    return;
                                }

                                if (field.name === 'accept_terms') {
                                    field.setCustomValidity('Debes aceptar los terminos y condiciones para continuar.');
                                    return;
                                }

                            field.setCustomValidity('Completa este campo.');
                            return;
                        }

                        if (field.type === 'email' || field.name === 'email') {
                            const message = emailError(field.value);
                            if (message) {
                                field.setCustomValidity(message);
                                return;
                            }
                            return;
                        }

                        field.setCustomValidity('Completa este campo correctamente.');
                    });

                        if (field.type === 'email' || field.name === 'email') {
                            field.setCustomValidity(emailError(field.value));
                            return;
                        }

                        field.setCustomValidity('');
                        field.setCustomValidity('');
                    });
                });

                if (email) {
                    email.addEventListener('input', () => {
                        const message = emailError(email.value);
                        if (message) {
                            setHint(message);
                            return;
                        }
                        if (registerHints && registerHints.textContent === message) {
                            setHint('');
                        }
                    });
                }

                if (password && passwordConfirmation) {
                    const checkMatch = () => {
                        if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                            passwordConfirmation.setCustomValidity('Las contrasenas no coinciden.');
                            setHint('Las contrasenas no coinciden.');
                        } else {
                            passwordConfirmation.setCustomValidity('');
                            if (registerHints && registerHints.textContent === 'Las contrasenas no coinciden.') {
                                setHint('');
                            }
                        }
                    };

                    password.addEventListener('input', checkMatch);
                    passwordConfirmation.addEventListener('input', checkMatch);
                }
            });
        })();
    </script>

</body>
</html>
