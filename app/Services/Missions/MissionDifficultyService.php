<?php

declare(strict_types=1);

namespace App\Services\Missions;

class MissionDifficultyService
{
    /**
     * @return array{key: string, label: string, boss_multiplier: float, race_points_multiplier: float}
     */
    public function resolveTier(int $dangerScore): array
    {
        $tiers = config('missions.difficulty.tiers', []);

        foreach ($tiers as $key => $tier) {
            $min = $tier['min'] ?? null;
            $max = $tier['max'] ?? null;
            if ($min === null || $max === null) {
                continue;
            }
            if ($dangerScore >= $min && $dangerScore <= $max) {
                return [
                    'key' => $key,
                    'label' => $tier['label'] ?? $key,
                    'boss_multiplier' => (float) ($tier['boss_multiplier'] ?? 1.0),
                    'race_points_multiplier' => (float) ($tier['race_points_multiplier'] ?? 1.0),
                ];
            }
        }

        $fallback = array_key_first($tiers);
        $fallbackTier = $fallback ? $tiers[$fallback] : [];

        return [
            'key' => $fallback ?? 'easy',
            'label' => $fallbackTier['label'] ?? 'Easy',
            'boss_multiplier' => (float) ($fallbackTier['boss_multiplier'] ?? 1.0),
            'race_points_multiplier' => (float) ($fallbackTier['race_points_multiplier'] ?? 1.0),
        ];
    }
}
