<?php

declare(strict_types=1);

namespace App\Services\Stats;

use App\Models\Character;
use App\Models\Mount;
use Illuminate\Support\Facades\Schema;

class CharacterStatsCalculator
{
    public function __construct(private StatMultipliers $multipliers)
    {
    }

    /**
     * @return array<string, int>
     */
    public function getBaseStats(Character $character): array
    {
        $stats = config('stats.stats', []);
        $defaults = ['hp' => 8, 'attack' => 8, 'defense' => 8, 'speed' => 8, 'magic' => 8];

        $base = [];
        foreach ($stats as $stat) {
            $base[$stat] = $this->readBaseStat($character, $stat, $defaults[$stat] ?? 0);
        }

        return $base;
    }

    /**
     * @return array<string, int>
     */
    public function getTotalStats(Character $character): array
    {
        $breakdown = $this->getBreakdown($character);

        return $breakdown['total_stats'];
    }

    /**
     * @return array<string, mixed>
     */
    public function getBreakdown(Character $character): array
    {
        $level = max(1, (int) ($character->level ?? 1));
        $base = $this->getBaseStats($character);
        $multipliers = $this->multipliers->getAllMultipliers($level);
        $equipmentBonus = $this->getEquipmentBonus($character);
        $scaled = [];
        $total = [];
        $outOfRange = [];

        $min = (int) config('stats.base_stat_min', 0);
        $max = (int) config('stats.base_stat_max', 12);
        $minimumTotals = config('stats.rounding.minimums', []);

        foreach ($base as $stat => $value) {
            if ($value < $min || $value > $max) {
                $outOfRange[] = $stat;
            }

            $multiplier = (float) ($multipliers[$stat] ?? 1.0);
            $scaledValue = (int) floor($value * $multiplier);
            $scaled[$stat] = $scaledValue;

            $bonus = (int) ($equipmentBonus[$stat] ?? 0);
            $totalValue = $scaledValue + $bonus;
            $minimum = (int) ($minimumTotals[$stat] ?? 0);
            if ($totalValue < $minimum) {
                $totalValue = $minimum;
            }
            $total[$stat] = $totalValue;
        }

        return [
            'level' => $level,
            'base_stats' => $base,
            'base_out_of_range' => $outOfRange,
            'multipliers_por_stat' => $multipliers,
            'scaled_stats' => $scaled,
            'equipment_bonus' => $equipmentBonus,
            'total_stats' => $total,
        ];
    }

    private function readBaseStat(Character $character, string $stat, int $default): int
    {
        $columns = ['hp', 'attack', 'defense', 'speed', 'magic'];
        if (Schema::hasColumn('characters', $stat)) {
            return (int) ($character->{$stat} ?? $default);
        }

        if ($stat === 'hp' && Schema::hasColumn('characters', 'hp_max')) {
            $value = $character->hp_max;
            if ($value !== null) {
                return (int) $value;
            }
        }

        $statsJson = is_array($character->stats_json) ? $character->stats_json : [];
        $legacyMap = [
            'attack' => 'fuerza',
            'magic' => 'magia',
            'defense' => 'defensa',
            'speed' => 'velocidad',
        ];

        if (array_key_exists($stat, $statsJson)) {
            return (int) $statsJson[$stat];
        }

        if (isset($legacyMap[$stat]) && array_key_exists($legacyMap[$stat], $statsJson)) {
            return (int) $statsJson[$legacyMap[$stat]];
        }

        if ($stat === 'hp' && array_key_exists('hp', $statsJson)) {
            return (int) $statsJson['hp'];
        }

        if (in_array($stat, $columns, true)) {
            return $default;
        }

        return $default;
    }

    /**
     * @return array<string, int>
     */
    private function getEquipmentBonus(Character $character): array
    {
        $bonus = ['hp' => 0, 'attack' => 0, 'defense' => 0, 'speed' => 0, 'magic' => 0];

        if (Schema::hasTable('character_equipment') && Schema::hasTable('items')) {
            $equipment = $character->equipment()->with('item')->get();
            foreach ($equipment as $row) {
                $item = $row->item;
                if (!$item) {
                    continue;
                }
                $bonus['hp'] += (int) ($item->bonus_hp ?? 0);
                $bonus['attack'] += (int) ($item->bonus_strength ?? 0);
                $bonus['magic'] += (int) ($item->bonus_magic ?? 0);
                $bonus['defense'] += (int) ($item->bonus_defense ?? 0);
                $bonus['speed'] += (int) ($item->bonus_speed ?? 0);
            }
        }

        if (Schema::hasTable('mounts') && $character->mount_id) {
            $mount = $character->mount ?? Mount::query()->find($character->mount_id);
            if ($mount) {
                $bonus['attack'] += (int) ($mount->bonus_strength ?? 0);
                $bonus['magic'] += (int) ($mount->bonus_magic ?? 0);
                $bonus['defense'] += (int) ($mount->bonus_defense ?? 0);
                $bonus['speed'] += (int) ($mount->bonus_speed ?? 0);
            }
        }

        return $bonus;
    }
}
