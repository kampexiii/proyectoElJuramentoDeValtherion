<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
            'password' => ['required', 'string'],
        ];

        if ($this->cookie('vth_cookie_consent') !== 'accepted') {
            $rules['accept_cookies'] = ['accepted'];
            $rules['accept_terms'] = ['accepted'];
        }

        return $rules;
    }

    /**
     * Mensajes personalizados para validacion en login.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe tener un formato valido.',
            'email.regex' => 'El correo debe tener formato usuario@dominio.com.',
            'email.max' => 'El correo no puede exceder 255 caracteres.',
            'password.required' => 'La contrasena es obligatoria.',
            'accept_cookies.accepted' => 'Debes aceptar las cookies para continuar.',
            'accept_terms.accepted' => 'Debes aceptar los terminos y condiciones para continuar.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
