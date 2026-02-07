<?php

declare(strict_types=1);

namespace App\Services\Missions;

use App\Enums\MissionRunStatus;
use App\Models\Character;
use App\Models\CharacterMissionRun;
use App\Models\CharacterMissionRunStep;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MissionRunService
{
    public function start(Mission $mission, Character $character): CharacterMissionRun
    {
        return DB::transaction(function () use ($mission, $character) {
            $existing = CharacterMissionRun::query()
                ->where('character_id', $character->id)
                ->whereIn('status', [MissionRunStatus::Active, MissionRunStatus::BossPending])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $startNode = MissionNode::query()
                ->where('mission_id', $mission->id)
                ->where('is_start', true)
                ->where('step_index', 1)
                ->first();

            if (!$startNode) {
                throw new RuntimeException('La mision no tiene nodo de inicio valido.');
            }

            return CharacterMissionRun::create([
                'character_id' => $character->id,
                'mission_id' => $mission->id,
                'status' => MissionRunStatus::Active,
                'current_node_id' => $startNode->id,
                'current_step_index' => 1,
                'danger_score' => 0,
                'wound_stacks' => 0,
                'accepted_at' => now(),
            ]);
        });
    }

    public function choose(CharacterMissionRun $run, MissionChoice $choice): CharacterMissionRun
    {
        return DB::transaction(function () use ($run, $choice) {
            $run = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();

            if ($run->status !== MissionRunStatus::Active) {
                return $run;
            }

            $currentStep = (int) $run->current_step_index;
            if ($run->current_node_id !== $choice->mission_node_id) {
                throw new RuntimeException('La opcion no pertenece al nodo actual.');
            }

            $existingStep = CharacterMissionRunStep::query()
                ->where('run_id', $run->id)
                ->where('step_index', $currentStep)
                ->first();

            if ($existingStep) {
                return $run;
            }

            $effects = is_array($choice->effects_json) ? $choice->effects_json : [];
            $wounds = (int) ($effects['wounds'] ?? 0);
            $heal = (int) ($effects['heal'] ?? 0);

            $run->danger_score += (int) $choice->difficulty_points;

            $woundDelta = $wounds + ((int) $choice->difficulty_points === 3 ? 1 : 0) - $heal;
            $run->wound_stacks = max(0, $run->wound_stacks + $woundDelta);

            CharacterMissionRunStep::create([
                'run_id' => $run->id,
                'step_index' => $currentStep,
                'node_id' => $choice->mission_node_id,
                'choice_id' => $choice->id,
                'difficulty_points_snapshot' => (int) $choice->difficulty_points,
                'effects_snapshot_json' => !empty($effects) ? $effects : null,
            ]);

            if ($currentStep >= 6) {
                $run->status = MissionRunStatus::BossPending;
                $run->current_node_id = null;
            } else {
                if (!$choice->next_node_id) {
                    throw new RuntimeException('La opcion no tiene siguiente nodo.');
                }
                $run->current_node_id = $choice->next_node_id;
                $run->current_step_index = $currentStep + 1;
            }

            $run->save();

            return $run;
        });
    }

    /**
     * @return array{run: CharacterMissionRun, partial_xp: int}
     */
    public function abandonWithPartialXp(CharacterMissionRun $run, Character $character): array
    {
        return DB::transaction(function () use ($run, $character) {
            $run = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();

            if ($run->character_id !== $character->id) {
                throw new RuntimeException('No puedes abandonar este run.');
            }

            if ($run->status === MissionRunStatus::BossPending) {
                throw new RuntimeException('No puedes abandonar con XP parcial durante el boss. Rindete en la batalla.');
            }

            if ($run->status !== MissionRunStatus::Active) {
                throw new RuntimeException('La mision no esta activa.');
            }

            $alreadyAwarded = $run->partial_xp_awarded_at !== null;
            $mission = $run->mission()->with('reward')->first();
            $rewardXp = $mission?->reward ? (int) $mission->reward->xp : 0;
            $partialXp = $alreadyAwarded ? 0 : (int) floor($rewardXp * 0.10);

            if (!$alreadyAwarded && $partialXp > 0) {
                Character::query()->whereKey($character->id)->increment('xp', $partialXp);
            }

            if (!$alreadyAwarded) {
                $run->partial_xp_amount = $partialXp;
                $run->partial_xp_awarded_at = now();
            }

            $run->status = MissionRunStatus::Abandoned;
            $run->current_node_id = null;
            $run->completed_at = now();
            $run->abandoned_at = now();
            $run->save();

            return ['run' => $run, 'partial_xp' => $partialXp];
        });
    }

    public function abandon(CharacterMissionRun $run): CharacterMissionRun
    {
        $run->status = MissionRunStatus::Abandoned;
        $run->current_node_id = null;
        $run->completed_at = now();
        $run->save();

        return $run;
    }
}
