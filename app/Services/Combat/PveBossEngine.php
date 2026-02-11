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
    private const BOSS_HP_MULT = 0.55;
    private const BOSS_ATK_MULT = 0.60;
    private const BOSS_DEF_MULT = 0.50;
    private const BASE_POWER = 6.0;
    private const DEF_K = 8.0;
    private const DEFEND_DAMAGE_MULT = 0.70;
    private const BOSS_MIN_HIT_PCT = 0.01;
    private const BOSS_PLAYER_MAX_HIT_PCT = 0.35;

    public function __construct(
        private CharacterStatsCalculator $statsCalculator,
        private MissionDifficultyService $difficultyService
    ) {}

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

        $bossHpMax = max(1, (int) round($bossHpBase * $tierConfig['boss_hp_mult'] * self::BOSS_HP_MULT));
        $bossAttack = max(1, (int) round($bossAttackBase * $tierConfig['boss_atk_mult'] * self::BOSS_ATK_MULT));
        $bossDefense = max(0, (int) round($bossDefenseBase * $tierConfig['boss_def_mult'] * self::BOSS_DEF_MULT));

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

        $p1DefEff = (int) ($statsP1['defense'] ?? 0);
        $p2DefEff = (int) ($statsP2['defense'] ?? 0);

        $notes = [];
        $notes[] = 'Turno ' . $turnNumber;

        $damageToP1 = 0;
        $damageToP2 = 0;
        $damageInfoToP1 = $this->emptyDamageInfo($p1DefEff, $p1Defending);
        $damageInfoToP2 = $this->emptyDamageInfo($p2DefEff, $p2Defending);
        $secondSkipped = false;

        $potionsLeft = $battle->p1_potions_left ?? (int) config('combat.potion.max_charges', 2);
        $healAmount = 0;

        $p1HpBefore = $p1Hp;
        $p2HpBefore = $p2Hp;

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
            $damageInfoToP2 = $this->computePveDamage($playerAction, $statsP1, $p2DefEff, $p2Defending);
            $damageToP2 = $this->applyDamageCap($damageInfoToP2['final'], $p2HpMax, $maxHitPct);
            $damageToP2 = max($damageToP2, $this->minBossDamage($p2HpMax));
            $p2Hp -= $damageToP2;
        }

        $p1HpBeforeBoss = $p1Hp;

        if ($p2Hp <= 0) {
            $secondSkipped = true;
        } else {
            if ($bossAction === 'attack') {
                $damageInfoToP1 = $this->computePveDamage('attack', $statsP2, $p1DefEff, $p1Defending);
                $damageToP1 = $this->applyDamageCap($damageInfoToP1['final'], $p1HpMax, self::BOSS_PLAYER_MAX_HIT_PCT);
                $p1Hp -= $damageToP1;
            }
        }

        $p1Hp = max(0, $p1Hp);
        $p2Hp = max(0, $p2Hp);

        $notes[] = 'Jugador usa ' . $this->label($playerAction) . '.';
        $notes[] = 'Boss usa ' . $this->label($bossAction) . '.';
        $notes[] = sprintf(
            'Daño al boss: %d (raw %.2f). ATK %.2f vs DEF %.2f. DefendMult %.2f. HP boss: %d -> %d.',
            $damageToP2,
            $damageInfoToP2['raw'],
            $damageInfoToP2['atk_used'],
            $damageInfoToP2['def_used'],
            $damageInfoToP2['defend_mult'],
            $p2HpBefore,
            $p2Hp
        );
        $notes[] = sprintf(
            'Daño al jugador: %d (raw %.2f). ATK %.2f vs DEF %.2f. DefendMult %.2f. HP jugador: %d -> %d.',
            $damageToP1,
            $damageInfoToP1['raw'],
            $damageInfoToP1['atk_used'],
            $damageInfoToP1['def_used'],
            $damageInfoToP1['defend_mult'],
            $p1HpBeforeBoss,
            $p1Hp
        );
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

    /**
     * @return array{raw: float, final: int, atk_used: float, def_used: float, defend_mult: float}
     */
    private function computePveDamage(string $action, array $attackerStats, int $targetDefEff, bool $targetDefending): array
    {
        if ($action !== 'attack' && $action !== 'magic') {
            return $this->emptyDamageInfo($targetDefEff, $targetDefending);
        }

        $attack = (float) ($attackerStats['attack'] ?? 0);
        $magic = (float) ($attackerStats['magic'] ?? 0);
        $atkUsed = $action === 'magic'
            ? ($magic > 0 ? $magic : ($attack * 0.90))
            : $attack;

        $defendMult = $targetDefending ? self::DEFEND_DAMAGE_MULT : 1.0;
        $raw = ($atkUsed * self::BASE_POWER) / ((float) $targetDefEff + self::DEF_K);
        $raw *= $defendMult;
        $raw = max(0.25, $raw);

        return [
            'raw' => $raw,
            'final' => max(1, (int) round($raw)),
            'atk_used' => $atkUsed,
            'def_used' => (float) $targetDefEff,
            'defend_mult' => $defendMult,
        ];
    }

    /**
     * @return array{raw: float, final: int, atk_used: float, def_used: float, defend_mult: float}
     */
    private function emptyDamageInfo(int $targetDefEff, bool $targetDefending): array
    {
        return [
            'raw' => 0.0,
            'final' => 0,
            'atk_used' => 0.0,
            'def_used' => (float) $targetDefEff,
            'defend_mult' => $targetDefending ? self::DEFEND_DAMAGE_MULT : 1.0,
        ];
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

    private function minBossDamage(int $bossHpMax): int
    {
        return max(1, (int) round($bossHpMax * self::BOSS_MIN_HIT_PCT));
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
