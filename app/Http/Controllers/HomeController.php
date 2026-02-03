<?php

namespace App\Http\Controllers;

use App\Services\MonthlyChronicleService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(MonthlyChronicleService $svc, Request $request)
    {
        $data = $svc->previousMonth();
        $data['character'] = $request->user()?->character;

        $spriteUrl = null;
        if ($data['character']) {
            // Primero intentar sprite personal
            $candidate = public_path("assets/characters/{$data['character']->id}.png");
            if (file_exists($candidate)) {
                $spriteUrl = asset("assets/characters/{$data['character']->id}.png");
            } else {
                // Usar sprite de raza
                $spriteUrl = $data['character']->race?->sprite_url;
            }
        }
        if ($data['character'] && !$spriteUrl) {
            $spriteUrl = asset('assets/sprites/razas/human.png');
        }
        $data['spriteUrl'] = $spriteUrl;

            // Pasar el modelo completo para que effectiveStats() esté disponible en la vista
            return view('game.home.index', [
                'character' => $data['character'],
                'spriteUrl' => $spriteUrl,
            ]);
    }
}
