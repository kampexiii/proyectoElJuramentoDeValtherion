<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $response = redirect()->intended(route('dashboard', absolute: false));

        if ($request->cookie('vth_cookie_consent') !== 'accepted' && $request->boolean('accept_cookies') && $request->boolean('accept_terms')) {
            $response->withCookie(\Illuminate\Support\Facades\Cookie::forever('vth_cookie_consent', 'accepted'));
        }

        return $response;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $cookieName = config('session.cookie');
        $domain = $request->getHost();

        $response = redirect('/');

        $response->withCookie(Cookie::forget($cookieName));
        $response->withCookie(Cookie::forget($cookieName, '/', $domain));
        $response->withCookie(Cookie::forget('XSRF-TOKEN'));
        $response->withCookie(Cookie::forget('XSRF-TOKEN', '/', $domain));

        return $response;
    }
}
