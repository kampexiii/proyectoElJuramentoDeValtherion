<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'accept_cookies' => ['accepted'],
            'accept_terms' => ['accepted'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe tener un formato valido.',
            'email.regex' => 'El correo debe tener formato usuario@dominio.com.',
            'email.max' => 'El correo no puede exceder 255 caracteres.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.confirmed' => 'Las contrasenas no coinciden.',
            'accept_cookies.accepted' => 'Debes aceptar las cookies para registrarte.',
            'accept_terms.accepted' => 'Debes aceptar los terminos y condiciones para registrarte.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $cookie = \Illuminate\Support\Facades\Cookie::forever('vth_cookie_consent', 'accepted');

        return redirect(route('dashboard', absolute: false))->withCookie($cookie);
    }
}
