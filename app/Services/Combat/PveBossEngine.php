<?php

declare(strict_types=1);

namespace App\Services\Combat;

use App\Models\Battle;

class PveBossEngine
{
    public function resolveTurn(Battle $battle, string $playerAction, string $bossAction): array
    {
        $statsP1 = $battle->stats_p1_json ?? [];
        $statsP2 = $battle->stats_p2_json ?? [];

        $p1Hp = (int) $battle->p1_hp;
        $p2Hp = (int) $battle->p2_hp;

        $p1Defending = $playerAction === 'defend';
        $p2Defending = $bossAction === 'defend';

        $p1Defense = (int) ($statsP1['defense'] ?? 0);
        $p2Defense = (int) ($statsP2['defense'] ?? 0);

        $defMultiplier = (float) config('combat.defend_defense_multiplier', 1.8);
        $p1DefEff = $p1Defending ? (int) floor($p1Defense * $defMultiplier) : $p1Defense;
        $p2DefEff = $p2Defending ? (int) floor($p2Defense * $defMultiplier) : $p2Defense;

        $damageToP1 = 0;
        $damageToP2 = 0;
        $secondSkipped = false;

        $damageToP2 = $this->computePlayerDamage($playerAction, $statsP1, $p2DefEff);
        $p2Hp -= $damageToP2;

        if ($p2Hp <= 0) {
            $secondSkipped = true;
        } else {
            $damageToP1 = $this->computeBossDamage($bossAction, $statsP2, $p1DefEff);
            $p1Hp -= $damageToP1;
        }

        $p1Hp = max(0, $p1Hp);
        $p2Hp = max(0, $p2Hp);

        $result = null;
        if ($p2Hp <= 0) {
            $result = 'p1_win';
        } elseif ($p1Hp <= 0) {
            $result = 'p2_win';
        }

        return [
            'first_actor' => 'p1',
            'p1_defending' => $p1Defending,
            'p2_defending' => $p2Defending,
            'damage_to_p1' => $damageToP1,
            'damage_to_p2' => $damageToP2,
            'p1_hp' => $p1Hp,
            'p2_hp' => $p2Hp,
            'second_skipped' => $secondSkipped,
            'result' => $result,
        ];
    }

    public function bossActionForTurn(int $turnNumber): string
    {
        return $turnNumber % 3 === 0 ? 'attack' : 'defend';
    }

    private function computePlayerDamage(string $action, array $attackerStats, int $targetDefEff): int
    {
        $attack = (int) ($attackerStats['attack'] ?? 0);
        $magic = (int) ($attackerStats['magic'] ?? 0);

        $attackDefMultiplier = (float) config('combat.damage.attack_defense_multiplier', 0.6);
        $magicMultiplier = (float) config('combat.damage.magic_multiplier', 1.2);
        $magicDefMultiplier = (float) config('combat.damage.magic_defense_multiplier', 0.3);

        return match ($action) {
            'attack' => max(1, (int) floor($attack - ($targetDefEff * $attackDefMultiplier))),
            'magic' => max(1, (int) floor(($magic * $magicMultiplier) - ($targetDefEff * $magicDefMultiplier))),
            default => 0,
        };
    }

    private function computeBossDamage(string $action, array $attackerStats, int $targetDefEff): int
    {
        if ($action !== 'attack') {
            return 0;
        }

        $attack = (int) ($attackerStats['attack'] ?? 0);
        $attackDefMultiplier = (float) config('combat.damage.attack_defense_multiplier', 0.6);

        return max(1, (int) floor($attack - ($targetDefEff * $attackDefMultiplier)));
    }
}
