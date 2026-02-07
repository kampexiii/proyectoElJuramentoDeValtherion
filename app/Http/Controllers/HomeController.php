<?php

namespace App\Http\Controllers;

use App\Services\MonthlyChronicleService;
use App\Services\Stats\CharacterStatsCalculator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(MonthlyChronicleService $svc, Request $request, CharacterStatsCalculator $calculator)
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

        $statsBase = [];
        $statsMultipliers = [];
        $statsCurrent = [];
        $statsCurrentMultiplied = [];
        if ($data['character']) {
            $breakdown = $calculator->getBreakdown($data['character']);
            $statsBase = $breakdown['base_stats'] ?? [];
            $statsMultipliers = $breakdown['multipliers_por_stat'] ?? [];

            $rawEffective = method_exists($data['character'], 'effectiveStats')
                ? $data['character']->effectiveStats()
                : (is_array($data['character']->stats_json) ? $data['character']->stats_json : []);

            $statsCurrent = $statsBase;
            $statsCurrent['hp'] = (int) ($data['character']->hp_max ?? ($statsBase['hp'] ?? 0));
            $statsCurrent['attack'] = (int) ($rawEffective['attack'] ?? $rawEffective['fuerza'] ?? ($statsBase['attack'] ?? 0));
            $statsCurrent['defense'] = (int) ($rawEffective['defense'] ?? $rawEffective['defensa'] ?? ($statsBase['defense'] ?? 0));
            $statsCurrent['speed'] = (int) ($rawEffective['speed'] ?? $rawEffective['velocidad'] ?? ($statsBase['speed'] ?? 0));
            $statsCurrent['magic'] = (int) ($rawEffective['magic'] ?? $rawEffective['magia'] ?? ($statsBase['magic'] ?? 0));

            $statKeys = ['hp', 'attack', 'defense', 'speed', 'magic'];
            foreach ($statKeys as $key) {
                $current = (int) ($statsCurrent[$key] ?? 0);
                $multiplier = (float) ($statsMultipliers[$key] ?? 1.0);
                $statsCurrentMultiplied[$key] = (int) floor($current * $multiplier);
            }
        }

            // Pasar el modelo completo para que effectiveStats() esté disponible en la vista
            return view('game.home.index', array_merge($data, [
                'character' => $data['character'],
                'spriteUrl' => $spriteUrl,
                'statsBase' => $statsBase,
                'statsCurrent' => $statsCurrent,
                'statsCurrentMultiplied' => $statsCurrentMultiplied,
                'statsMultipliers' => $statsMultipliers,
            ]));
    }
}
