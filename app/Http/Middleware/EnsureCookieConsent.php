<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCookieConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $consent = $request->cookie('vth_cookie_consent');

        if ($consent !== 'accepted') {
            $acceptCookies = $request->boolean('accept_cookies');
            $acceptTerms = $request->boolean('accept_terms');

            if ($acceptCookies && $acceptTerms) {
                return $next($request);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['cookie' => 'Debes aceptar las cookies y los términos para registrarte.']);
        }

        return $next($request);
    }
}
