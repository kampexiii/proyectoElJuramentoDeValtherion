<?php

declare(strict_types=1);

namespace App\Services\Missions;

use App\Models\CharacterMissionRun;
use App\Models\RacePointsEvent;
use Illuminate\Support\Facades\DB;

class RacePointsService
{
    /**
     * @return array{points: int, message: string|null}
     */
    public function applyMissionPoints(CharacterMissionRun $run, array $tierData, bool $alreadyApplied): array
    {
        if ($alreadyApplied) {
            return ['points' => 0, 'message' => 'Los puntos ya fueron aplicados.'];
        }

        $character = $run->character()->first();
        $mission = $run->mission()->first();

        if (!$character || !$mission) {
            return ['points' => 0, 'message' => 'No se pudo cargar personaje o mision.'];
        }

        if (!$character->race_id) {
            return ['points' => 0, 'message' => 'El personaje no tiene raza asignada.'];
        }

        $basePoints = (int) $mission->base_race_points;
        $multiplier = (float) ($tierData['race_points_multiplier'] ?? 1.0);
        $rawPoints = (int) round($basePoints * $multiplier);

        if ($rawPoints <= 0) {
            return ['points' => 0, 'message' => 'La mision no otorga puntos.'];
        }

        $today = now()->toDateString();
        $monthKey = now()->format('Y-m');
        $cap = (int) config('missions.race_points_daily_cap', 200);

        if ($mission->repeatable) {
            $currentPoints = (int) DB::table('race_points_events')
                ->where('character_id', $character->id)
                ->where('source_type', 'mission')
                ->where('happened_on', $today)
                ->lockForUpdate()
                ->sum('points');

            $remaining = max(0, $cap - $currentPoints);
            if ($remaining <= 0) {
                return ['points' => 0, 'message' => 'Cap diario alcanzado.'];
            }

            $rawPoints = min($rawPoints, $remaining);
        }

        RacePointsEvent::create([
            'character_id' => $character->id,
            'race_id' => $character->race_id,
            'points' => $rawPoints,
            'source_type' => 'mission',
            'source_id' => $mission->id,
            'happened_on' => $today,
            'month_key' => $monthKey,
            'created_at' => now(),
        ]);

        return ['points' => $rawPoints, 'message' => null];
    }
}
