<?php

declare(strict_types=1);

namespace App\Services\Stats;

class StatMultipliers
{
    public function getMultiplier(string $stat, int $level): float
    {
        $data = config('stats.multipliers.' . $stat);
        $base = (float) ($data['base'] ?? 1.0);
        $growth = (float) ($data['growth'] ?? 0.0);
        $level = max(1, $level);

        return $base + ($growth * ($level - 1));
    }

    /**
     * @return array<string, float>
     */
    public function getAllMultipliers(int $level): array
    {
        $stats = config('stats.stats', []);
        $multipliers = [];

        foreach ($stats as $stat) {
            $multipliers[$stat] = $this->getMultiplier($stat, $level);
        }

        return $multipliers;
    }
}
