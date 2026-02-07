<?php

declare(strict_types=1);

namespace App\Services\Combat;

use App\Models\Battle;

class PvpCombatEngine
{
    public function resolveTurn(Battle $battle, string $p1Action, string $p2Action): array
    {
        $statsP1 = $battle->stats_p1_json ?? [];
        $statsP2 = $battle->stats_p2_json ?? [];

        $p1Hp = (int) $battle->p1_hp;
        $p2Hp = (int) $battle->p2_hp;

        $p1Speed = (int) ($statsP1['speed'] ?? 0);
        $p2Speed = (int) ($statsP2['speed'] ?? 0);

        $firstActor = $this->decideFirstActor($p1Speed, $p2Speed);

        $p1Defending = $p1Action === 'defend';
        $p2Defending = $p2Action === 'defend';

        $p1Defense = (int) ($statsP1['defense'] ?? 0);
        $p2Defense = (int) ($statsP2['defense'] ?? 0);

        $defMultiplier = (float) config('combat.defend_defense_multiplier', 1.8);
        $p1DefEff = $p1Defending ? (int) floor($p1Defense * $defMultiplier) : $p1Defense;
        $p2DefEff = $p2Defending ? (int) floor($p2Defense * $defMultiplier) : $p2Defense;

        $damageToP1 = 0;
        $damageToP2 = 0;
        $secondSkipped = false;

        if ($firstActor === 'p1') {
            $damageToP2 = $this->computeDamage($p1Action, $statsP1, $p2DefEff);
            $p2Hp -= $damageToP2;

            if ($p2Hp <= 0) {
                $secondSkipped = true;
            } else {
                $damageToP1 = $this->computeDamage($p2Action, $statsP2, $p1DefEff);
                $p1Hp -= $damageToP1;
            }
        } else {
            $damageToP1 = $this->computeDamage($p2Action, $statsP2, $p1DefEff);
            $p1Hp -= $damageToP1;

            if ($p1Hp <= 0) {
                $secondSkipped = true;
            } else {
                $damageToP2 = $this->computeDamage($p1Action, $statsP1, $p2DefEff);
                $p2Hp -= $damageToP2;
            }
        }

        $p1Hp = max(0, $p1Hp);
        $p2Hp = max(0, $p2Hp);

        $result = null;
        if ($p1Hp <= 0 && $p2Hp <= 0) {
            $result = 'draw';
        } elseif ($p2Hp <= 0) {
            $result = 'p1_win';
        } elseif ($p1Hp <= 0) {
            $result = 'p2_win';
        }

        return [
            'first_actor' => $firstActor,
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

    private function decideFirstActor(int $p1Speed, int $p2Speed): string
    {
        if ($p1Speed > $p2Speed) {
            return 'p1';
        }

        if ($p2Speed > $p1Speed) {
            return 'p2';
        }

        if ((bool) config('combat.speed_tie_rng', true)) {
            return random_int(1, 2) === 1 ? 'p1' : 'p2';
        }

        return 'p1';
    }

    private function computeDamage(string $action, array $attackerStats, int $targetDefEff): int
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
}
