<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Item;
use App\Models\Mount;

class StatCalculatorService
{
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
        return [
            'fuerza' => $stats['fuerza'] + $bonus['fuerza'],
            'magia' => $stats['magia'] + $bonus['magia'],
            'defensa' => $stats['defensa'] + $bonus['defensa'],
            'velocidad' => $stats['velocidad'] + $bonus['velocidad'],
        ];
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
