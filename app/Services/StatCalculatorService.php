<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Item;
use App\Models\Mount;
use App\Models\Race;
use Illuminate\Support\Facades\Schema;

class StatCalculatorService
{
    /**
     * Calcula los stats efectivos para un personaje, usando un equipo simulado (preview).
     */
    public function effectiveStatsForPreview($character, $equipment): array
    {
        $base = $character->stats_json ?? [];
        $race = $character->race;
        $stats = [
            'fuerza' => (int)($base['fuerza'] ?? $race->base_strength ?? 0),
            'magia' => (int)($base['magia'] ?? $race->base_magic ?? 0),
            'defensa' => (int)($base['defensa'] ?? $race->base_defense ?? 0),
            'velocidad' => (int)($base['velocidad'] ?? $race->base_speed ?? 0),
        ];

        // Bonificadores de equipo simulado
        $bonus = [
            'fuerza' => 0,
            'magia' => 0,
            'defensa' => 0,
            'velocidad' => 0,
        ];
        foreach ($equipment as $slot => $entry) {
            $item = $entry->item ?? null;
            if ($item) {
                if ($slot === 'mount' && $item instanceof \App\Models\Mount) {
                    $bonus = $this->sumBonuses($bonus, $this->getMountBonuses($item));
                } elseif ($item instanceof \App\Models\Item) {
                    $bonus = $this->sumBonuses($bonus, $this->getItemBonuses($item));
                }
            }
        }

        // Si hay montura especial "max stats" equipada
        $maxStats = false;
        foreach ($equipment as $slot => $entry) {
            $item = $entry->item ?? null;
            if ($item && $slot === 'mount' && ($item->code ?? null) === 'mount_legendario_caos') {
                $maxStats = true;
                break;
            }
            if ($item && $slot === 'mount' && ($item->bonuses_json['mode'] ?? null) === 'max_stats') {
                $maxStats = true;
                break;
            }
        }
        if ($maxStats && $race && is_array($race->caps_json) && count($race->caps_json) > 0) {
            return [
                'fuerza' => (int)($race->caps_json['fuerza'] ?? $race->caps_json['strength'] ?? $race->base_strength ?? 0),
                'magia' => (int)($race->caps_json['magia'] ?? $race->caps_json['magic'] ?? $race->base_magic ?? 0),
                'defensa' => (int)($race->caps_json['defensa'] ?? $race->caps_json['defense'] ?? $race->base_defense ?? 0),
                'velocidad' => (int)($race->caps_json['velocidad'] ?? $race->caps_json['speed'] ?? $race->base_speed ?? 0),
            ];
        }
        // Suma base + bonus
        $raw = [
            'fuerza' => $stats['fuerza'] + $bonus['fuerza'],
            'magia' => $stats['magia'] + $bonus['magia'],
            'defensa' => $stats['defensa'] + $bonus['defensa'],
            'velocidad' => $stats['velocidad'] + $bonus['velocidad'],
        ];

        return $this->clampToCaps($raw, $race);
    }

    /**
     * Calcula los stats efectivos de un personaje según su equipo y montura.
     * @param Character $character
     * @return array
     */
    public function effectiveStatsFor(Character $character): array
    {
        $base = $character->stats_json ?? [];
        $race = $character->race;
        $stats = [
            'fuerza' => (int)($base['fuerza'] ?? $race->base_strength ?? 0),
            'magia' => (int)($base['magia'] ?? $race->base_magic ?? 0),
            'defensa' => (int)($base['defensa'] ?? $race->base_defense ?? 0),
            'velocidad' => (int)($base['velocidad'] ?? $race->base_speed ?? 0),
        ];

        // Bonificadores de equipo
        $bonus = [
            'fuerza' => 0,
            'magia' => 0,
            'defensa' => 0,
            'velocidad' => 0,
        ];
        if (method_exists($character, 'equipment')) {
            foreach ($character->equipment as $equip) {
                $item = $equip->item ?? null;
                if ($item) {
                    $bonus = $this->sumBonuses($bonus, $this->getItemBonuses($item));
                }
            }
        }

        // Bonificador de montura (si existe tabla character_mount_equipment)
        if (method_exists($character, 'mount')) {
            $mount = $character->mount;
            if ($mount) {
                $bonus = $this->sumBonuses($bonus, $this->getMountBonuses($mount));
            }
        }

        // Si hay montura especial "max stats" equipada
        $maxStats = false;
        if (method_exists($character, 'equipment')) {
            foreach ($character->equipment as $equip) {
                $item = $equip->item ?? null;
                if ($item && (($item->code ?? null) === 'mount_legendario_caos' || ($item->bonuses_json['mode'] ?? null) === 'max_stats')) {
                    $maxStats = true;
                    break;
                }
            }
        }
        if (!$maxStats && method_exists($character, 'mount')) {
            $mount = $character->mount;
            if ($mount && (($mount->code ?? null) === 'mount_legendario_caos' || ($mount->bonuses_json['mode'] ?? null) === 'max_stats')) {
                $maxStats = true;
            }
        }
        if ($maxStats && $race && is_array($race->caps_json) && count($race->caps_json) > 0) {
            // Devolver caps máximos de la raza
            return [
                'fuerza' => (int)($race->caps_json['fuerza'] ?? $race->caps_json['strength'] ?? $race->base_strength ?? 0),
                'magia' => (int)($race->caps_json['magia'] ?? $race->caps_json['magic'] ?? $race->base_magic ?? 0),
                'defensa' => (int)($race->caps_json['defensa'] ?? $race->caps_json['defense'] ?? $race->base_defense ?? 0),
                'velocidad' => (int)($race->caps_json['velocidad'] ?? $race->caps_json['speed'] ?? $race->base_speed ?? 0),
            ];
        }
        // Suma base + bonus
        $raw = [
            'fuerza' => $stats['fuerza'] + $bonus['fuerza'],
            'magia' => $stats['magia'] + $bonus['magia'],
            'defensa' => $stats['defensa'] + $bonus['defensa'],
            'velocidad' => $stats['velocidad'] + $bonus['velocidad'],
        ];

        return $this->clampToCaps($raw, $race);
    }

    private function clampToCaps(array $stats, ?Race $race): array
    {
        if (!$race) {
            return $stats;
        }

        $caps = is_array($race->caps_json ?? null) ? $race->caps_json : [];
        $max = [
            'fuerza' => $this->capDe($caps, 'fuerza', 'strength'),
            'magia' => $this->capDe($caps, 'magia', 'magic'),
            'defensa' => $this->capDe($caps, 'defensa', 'defense'),
            'velocidad' => $this->capDe($caps, 'velocidad', 'speed'),
        ];

        $tieneCaps = collect($max)->filter()->count() > 0;
        if (!$tieneCaps && Schema::hasTable('races')) {
            $max = [
                'fuerza' => (int) Race::max('base_strength'),
                'magia' => (int) Race::max('base_magic'),
                'defensa' => (int) Race::max('base_defense'),
                'velocidad' => (int) Race::max('base_speed'),
            ];
        }

        $clamped = [];
        foreach ($stats as $key => $value) {
            $limit = $max[$key] ?? $value;
            $clamped[$key] = min((int) $value, (int) $limit);
        }

        return $clamped;
    }

    private function capDe(array $caps, string $es, string $en): ?int
    {
        if (array_key_exists($es, $caps)) {
            return (int) $caps[$es];
        }

        if (array_key_exists($en, $caps)) {
            return (int) $caps[$en];
        }

        return null;
    }

    private function sumBonuses(array $a, array $b): array
    {
        return [
            'fuerza' => ($a['fuerza'] ?? 0) + ($b['fuerza'] ?? 0),
            'magia' => ($a['magia'] ?? 0) + ($b['magia'] ?? 0),
            'defensa' => ($a['defensa'] ?? 0) + ($b['defensa'] ?? 0),
            'velocidad' => ($a['velocidad'] ?? 0) + ($b['velocidad'] ?? 0),
        ];
    }

    private function getItemBonuses(Item $item): array
    {
        $b = $item->bonuses_json ?? [];
        return [
            'fuerza' => (int)($b['fuerza'] ?? $b['strength'] ?? $item->bonus_strength ?? 0),
            'magia' => (int)($b['magia'] ?? $b['magic'] ?? $item->bonus_magic ?? 0),
            'defensa' => (int)($b['defensa'] ?? $b['defense'] ?? $item->bonus_defense ?? 0),
            'velocidad' => (int)($b['velocidad'] ?? $b['speed'] ?? $item->bonus_speed ?? 0),
        ];
    }

    private function getMountBonuses(Mount $mount): array
    {
        $b = $mount->bonuses_json ?? [];
        return [
            'fuerza' => (int)($b['fuerza'] ?? $b['strength'] ?? $mount->bonus_strength ?? 0),
            'magia' => (int)($b['magia'] ?? $b['magic'] ?? $mount->bonus_magic ?? 0),
            'defensa' => (int)($b['defensa'] ?? $b['defense'] ?? $mount->bonus_defense ?? 0),
            'velocidad' => (int)($b['velocidad'] ?? $b['speed'] ?? $mount->bonus_speed ?? 0),
        ];
    }
}
