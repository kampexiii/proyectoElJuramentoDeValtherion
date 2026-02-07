<?php

declare(strict_types=1);

namespace App\Services\Combat;

use App\Enums\BattleStatus;
use App\Models\BattleTurn;
use App\Models\CharacterMissionRun;
use App\Models\Battle;
use App\Services\Missions\MissionDifficultyService;
use App\Services\Stats\CharacterStatsCalculator;
use RuntimeException;

class PveBossEngine
{
    public function __construct(
        private CharacterStatsCalculator $statsCalculator,
        private MissionDifficultyService $difficultyService
    ) {
    }

    public function startBattleIfMissing(CharacterMissionRun $run): Battle
    {
        $existing = Battle::query()
            ->where('mission_run_id', $run->id)
            ->where('type', 'pve')
            ->latest('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        if ($run->status->value !== 'boss_pending') {
            throw new RuntimeException('La mision no esta lista para el boss.');
        }

        $mission = $run->mission()->with('finalBoss')->firstOrFail();
        $boss = $mission->finalBoss;
        if (!$boss) {
            throw new RuntimeException('La mision no tiene boss final.');
        }

        $bossStats = is_array($boss->base_stats_json) ? $boss->base_stats_json : [];
        $bossHpBase = (int) ($bossStats['hp'] ?? 0);
        $bossAttackBase = (int) ($bossStats['attack'] ?? $bossStats['damage'] ?? 0);
        $bossDefenseBase = (int) ($bossStats['defense'] ?? 0);

        if ($bossHpBase <= 0 || $bossAttackBase <= 0) {
            throw new RuntimeException('El boss no tiene stats completos (hp/attack/defense).');
        }

        $tier = $this->difficultyService->resolveTier((int) $run->danger_score);
        $tierKey = $tier['key'] ?? 'easy';
        $tierConfig = $this->tierConfig($tierKey);

        $bossHpMax = max(1, (int) floor($bossHpBase * $tierConfig['boss_hp_mult']));
        $bossAttack = max(1, (int) floor($bossAttackBase * $tierConfig['boss_atk_mult']));
        $bossDefense = max(0, (int) floor($bossDefenseBase * $tierConfig['boss_def_mult']));

        $character = $run->character()->firstOrFail();
        $totalStats = $this->statsCalculator->getTotalStats($character);

        $wounds = max(0, (int) $run->wound_stacks);
        $hpPenalty = max(0.0, 1 - ($wounds * (float) config('combat.wounds_hp_penalty_per_stack', 0.02)));
        $atkPenalty = max(0.0, 1 - ($wounds * (float) config('combat.wounds_atk_penalty_per_stack', 0.02)));

        $playerHpMax = max(1, (int) floor(((int) ($totalStats['hp'] ?? 1)) * $hpPenalty));
        $playerAttack = max(1, (int) floor(((int) ($totalStats['attack'] ?? 1)) * $atkPenalty));
        $playerDefense = max(0, (int) ($totalStats['defense'] ?? 0));
        $playerSpeed = max(0, (int) ($totalStats['speed'] ?? 0));
        $playerMagic = max(0, (int) ($totalStats['magic'] ?? 0));

        $potionsLeft = (int) config('combat.potion.max_charges', 2);

        return Battle::create([
            'room_id' => null,
            'type' => 'pve',
            'status' => BattleStatus::Active,
            'player1_character_id' => $character->id,
            'player2_character_id' => null,
            'final_boss_id' => $boss->id,
            'mission_run_id' => $run->id,
            'turn_number' => 1,
            'p1_hp' => $playerHpMax,
            'p2_hp' => $bossHpMax,
            'p1_defending' => false,
            'p2_defending' => false,
            'pending_p1_action' => null,
            'pending_p2_action' => null,
            'p1_potions_left' => $potionsLeft,
            'stats_p1_json' => [
                'hp_max' => $playerHpMax,
                'attack' => $playerAttack,
                'defense' => $playerDefense,
                'speed' => $playerSpeed,
                'magic' => $playerMagic,
            ],
            'stats_p2_json' => [
                'hp_max' => $bossHpMax,
                'attack' => $bossAttack,
                'defense' => $bossDefense,
                'speed' => 0,
                'magic' => 0,
                'tier_key' => $tierKey,
            ],
        ]);
    }

    public function resolveTurn(Battle $battle, string $playerAction): Battle
    {
        if ($battle->status !== BattleStatus::Active) {
            throw new RuntimeException('La batalla ya finalizo.');
        }

        $allowed = ['attack', 'magic', 'defend', 'potion'];
        if (!in_array($playerAction, $allowed, true)) {
            throw new RuntimeException('Accion invalida.');
        }

        $turnNumber = (int) $battle->turn_number;
        $bossAction = $this->bossActionForTurn($turnNumber);

        $statsP1 = $battle->stats_p1_json ?? [];
        $statsP2 = $battle->stats_p2_json ?? [];

        $p1HpMax = (int) ($statsP1['hp_max'] ?? $statsP1['hp'] ?? 0);
        $p2HpMax = (int) ($statsP2['hp_max'] ?? $statsP2['hp'] ?? 0);

        $tierKey = (string) ($statsP2['tier_key'] ?? 'easy');
        $tierConfig = $this->tierConfig($tierKey);
        $maxHitPct = (float) $tierConfig['max_hit_pct'];

        $p1Hp = (int) $battle->p1_hp;
        $p2Hp = (int) $battle->p2_hp;

        $p1Defending = $playerAction === 'defend';
        $p2Defending = $bossAction === 'defend';

        $defMultiplier = (float) config('combat.defend_defense_multiplier', 1.8);
        $p1DefEff = $this->effectiveDefense((int) ($statsP1['defense'] ?? 0), $p1Defending, $defMultiplier);
        $p2DefEff = $this->effectiveDefense((int) ($statsP2['defense'] ?? 0), $p2Defending, $defMultiplier);

        $notes = [];
        $notes[] = 'Turno ' . $turnNumber;

        $damageToP1 = 0;
        $damageToP2 = 0;
        $secondSkipped = false;

        $potionsLeft = $battle->p1_potions_left ?? (int) config('combat.potion.max_charges', 2);
        $healAmount = 0;

        if ($playerAction === 'potion') {
            if ($potionsLeft <= 0) {
                $notes[] = 'Jugador intenta usar pocion, pero no quedan.';
            } else {
                $healAmount = $this->potionHealAmount($p1HpMax);
                $p1Hp = min($p1HpMax, $p1Hp + $healAmount);
                $potionsLeft -= 1;
                $notes[] = 'Jugador usa pocion y cura ' . $healAmount . ' HP.';
            }
        } elseif ($playerAction === 'attack' || $playerAction === 'magic') {
            $damageToP2 = $this->computePlayerDamage($playerAction, $statsP1, $p2DefEff);
            $damageToP2 = $this->applyDamageCap($damageToP2, $p2HpMax, $maxHitPct);
            $p2Hp -= $damageToP2;
        }

        if ($p2Hp <= 0) {
            $secondSkipped = true;
        } else {
            if ($bossAction === 'attack') {
                $damageToP1 = $this->computeBossDamage($statsP2, $p1DefEff);
                $damageToP1 = $this->applyDamageCap($damageToP1, $p1HpMax, $maxHitPct);
                $p1Hp -= $damageToP1;
            }
        }

        $p1Hp = max(0, $p1Hp);
        $p2Hp = max(0, $p2Hp);

        $notes[] = 'Jugador usa ' . $this->label($playerAction) . '.';
        $notes[] = 'Boss usa ' . $this->label($bossAction) . '.';
        $notes[] = 'Danio al boss: ' . $damageToP2 . '.';
        $notes[] = 'Danio al jugador: ' . $damageToP1 . '.';
        $notes[] = 'HP jugador: ' . $p1Hp . ' / ' . ($p1HpMax > 0 ? $p1HpMax : 0) . '.';
        $notes[] = 'HP boss: ' . $p2Hp . ' / ' . ($p2HpMax > 0 ? $p2HpMax : 0) . '.';

        if ($secondSkipped) {
            $notes[] = 'El boss no actua por KO.';
        }

        $summary = sprintf(
            'T%d: P1 %s, P2 %s, danios %d/%d',
            $turnNumber,
            $this->label($playerAction),
            $this->label($bossAction),
            $damageToP1,
            $damageToP2
        );

        BattleTurn::create([
            'battle_id' => $battle->id,
            'turn_number' => $turnNumber,
            'p1_action' => $playerAction,
            'p2_action' => $bossAction,
            'first_actor' => 'p1',
            'damage_to_p1' => $damageToP1,
            'damage_to_p2' => $damageToP2,
            'notes_json' => [
                'summary' => $summary,
                'lines' => $notes,
                'potion_heal' => $healAmount,
            ],
        ]);

        $battle->p1_hp = $p1Hp;
        $battle->p2_hp = $p2Hp;
        $battle->p1_defending = false;
        $battle->p2_defending = false;
        $battle->pending_p1_action = null;
        $battle->pending_p2_action = null;
        $battle->p1_potions_left = $potionsLeft;
        $battle->turn_number = $turnNumber + 1;

        if ($p2Hp <= 0) {
            $battle->status = BattleStatus::Finished;
            $battle->result = 'p1_win';
            $battle->finished_at = now();
        } elseif ($p1Hp <= 0) {
            $battle->status = BattleStatus::Finished;
            $battle->result = 'p2_win';
            $battle->finished_at = now();
        }

        $battle->save();

        return $battle;
    }

    public function bossActionForTurn(int $turnNumber): string
    {
        $every = (int) config('combat.boss_attack_every_turns', 3);
        if ($every <= 0) {
            $every = 3;
        }

        return $turnNumber % $every === 0 ? 'attack' : 'defend';
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

    private function computeBossDamage(array $attackerStats, int $targetDefEff): int
    {
        $attack = (int) ($attackerStats['attack'] ?? 0);
        $attackDefMultiplier = (float) config('combat.damage.attack_defense_multiplier', 0.6);

        return max(1, (int) floor($attack - ($targetDefEff * $attackDefMultiplier)));
    }

    private function effectiveDefense(int $defense, bool $defending, float $multiplier): int
    {
        return $defending ? (int) floor($defense * $multiplier) : $defense;
    }

    private function applyDamageCap(int $damage, int $targetHpMax, float $capPct): int
    {
        if ($targetHpMax <= 0 || $capPct <= 0) {
            return $damage;
        }

        $cap = (int) floor($targetHpMax * $capPct);
        if ($cap <= 0) {
            return $damage;
        }

        return min($damage, $cap);
    }

    private function potionHealAmount(int $hpMax): int
    {
        $pct = (float) config('combat.potion.heal_pct', 0.30);

        return max(1, (int) floor($hpMax * $pct));
    }

    /**
     * @return array{max_hit_pct: float, boss_hp_mult: float, boss_atk_mult: float, boss_def_mult: float}
     */
    private function tierConfig(string $tierKey): array
    {
        $tiers = config('combat.pve.tiers', []);
        $tier = $tiers[$tierKey] ?? $tiers['easy'] ?? [];

        return [
            'max_hit_pct' => (float) ($tier['max_hit_pct'] ?? 0.45),
            'boss_hp_mult' => (float) ($tier['boss_hp_mult'] ?? 1.0),
            'boss_atk_mult' => (float) ($tier['boss_atk_mult'] ?? 1.0),
            'boss_def_mult' => (float) ($tier['boss_def_mult'] ?? 1.0),
        ];
    }

    private function label(string $action): string
    {
        return match ($action) {
            'attack' => 'Atacar',
            'magic' => 'Magia',
            'defend' => 'Defender',
            'potion' => 'Pocion',
            default => strtoupper($action),
        };
    }
}
