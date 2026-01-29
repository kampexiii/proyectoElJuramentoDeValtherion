<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasCharacter
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        if ($user->character) {
            return $next($request);
        }

        if ($request->routeIs('game.perfil')) {
            return $next($request);
        }

        if ($request->routeIs('game.ajustes')) {
            return $next($request);
        }

        if ($request->routeIs('game.personaje.create')) {
            return $next($request);
        }

        if ($request->routeIs('game.personaje.store')) {
            return $next($request);
        }

        return redirect()->route('game.personaje.create');
    }
}
