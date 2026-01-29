<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MonthlyChronicleService
{
    public function previousMonth(): array
    {
        // Calcular mes anterior
        $now = now();
        $prev = $now->copy()->subMonth();
        $py = (int) $prev->format('Y');
        $pm = (int) $prev->format('n');

        $season = null;
        if (Schema::hasTable('seasons')) {
            $season = DB::table('seasons')->where('year', $py)->where('month', $pm)->first();
        }

        $rankings = collect();
        $winner = null;

        if ($season) {
            if (Schema::hasTable('season_race_rankings') && Schema::hasTable('races')) {
                $rankings = DB::table('season_race_rankings')
                    ->where('season_id', $season->id)
                    ->join('races', 'season_race_rankings.race_id', '=', 'races.id')
                    ->select('season_race_rankings.race_id', 'season_race_rankings.points', 'races.name as race_name')
                    ->orderByDesc('season_race_rankings.points')
                    ->limit(10)
                    ->get();
            }

            if (Schema::hasTable('season_race_winners')) {
                $winner = DB::table('season_race_winners')->where('season_id', $season->id)->first();
                if ($winner && Schema::hasTable('races')) {
                    $winner->race_name = DB::table('races')->where('id', $winner->race_id)->value('name');
                }
            }
        }

        // Fallback estable: si no hay rankings, usar orden por nombre de raza (A). Si no existe 'races', mostrar mensaje (C).
        $fallbackUsed = null;
        $fallbackMessage = null;
        if ($rankings->isEmpty()) {
            if (Schema::hasTable('races')) {
                $races = DB::table('races')->select('id', 'name')->orderBy('name', 'asc')->get();
                $rankings = $races->values()->map(function ($r, $i) {
                    return (object) [
                        'race_id' => $r->id,
                        'race_name' => $r->name ?? ('Raza #' . $r->id),
                        'points' => 0,
                        'place' => $i + 1,
                    ];
                });
                $fallbackUsed = 'A';
            } else {
                $fallbackMessage = 'Aún no hay clasificación registrada. Falta cargar razas en la base de datos.';
                $fallbackUsed = 'C';
            }
        }

        return [
            'previousSeason' => $season,
            'seasonRankings' => $rankings,
            'seasonWinner' => $winner,
            'fallbackUsed' => $fallbackUsed,
            'fallbackMessage' => $fallbackMessage,
        ];
    }
}
