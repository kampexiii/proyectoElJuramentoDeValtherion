<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthenticatedSessionController extends Controller
{
    /**
     * Muestra el login de administración (forzando re-login si ya había sesión admin).
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            if ($request->user()->role !== 'admin') {
                return redirect()->route('home');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.admin-login');
    }

    /**
     * Procesa el login de administración y valida el rol.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if ($request->user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'No tienes permisos de administración.',
            ])->onlyInput('email');
        }

        return redirect()->intended(route('admin.index', absolute: false));
    }
}
