<?php

namespace App\Http\Controllers\Missions;

use App\Enums\MissionRunStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Missions\FightBossRequest;
use App\Models\CharacterMissionRun;
use App\Models\FinalBoss;
use App\Services\Missions\MissionDifficultyService;
use App\Services\Missions\MissionRewardService;
use App\Services\Missions\RacePointsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MissionBossController extends Controller
{
    public function show(Request $request, CharacterMissionRun $run, MissionDifficultyService $difficulty): \Illuminate\View\View
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $mission = $run->mission()->with('finalBoss')->firstOrFail();
        $tier = $difficulty->resolveTier((int) $run->danger_score);

        $combat = $this->resolveCombat($run, $mission->finalBoss, $tier);

        return view('missions.boss', [
            'mission' => $mission,
            'run' => $run,
            'tier' => $tier,
            'combat' => $combat,
        ]);
    }

    public function fight(FightBossRequest $request, CharacterMissionRun $run, MissionDifficultyService $difficulty, MissionRewardService $rewards, RacePointsService $points): RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $result = DB::transaction(function () use ($run, $difficulty, $rewards, $points) {
            $run = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();
            $mission = $run->mission()->with('finalBoss', 'reward')->firstOrFail();

            if ($run->status !== MissionRunStatus::BossPending) {
                return ['status' => 'blocked', 'message' => 'La mision no esta lista para boss.'];
            }

            $tier = $difficulty->resolveTier((int) $run->danger_score);
            $combat = $this->resolveCombat($run, $mission->finalBoss, $tier);

            if (!$combat['resolvable']) {
                return ['status' => 'error', 'message' => $combat['message']];
            }

            if (!$combat['win']) {
                $run->status = MissionRunStatus::Failed;
                $run->completed_at = now();
                $run->current_node_id = null;
                $run->save();

                return ['status' => 'lost', 'message' => 'Has perdido contra el boss.'];
            }

            $alreadyApplied = $run->rewards_applied_at !== null;

            if ($mission->reward) {
                $rewardResult = $rewards->applyRewards($mission->reward, $run->character_id, $mission->reward->items_json, $alreadyApplied);
            } else {
                $rewardResult = ['applied' => false, 'messages' => ['No hay rewards configurados.']];
            }

            $pointsResult = $points->applyMissionPoints($run, $tier, $alreadyApplied);

            $run->status = MissionRunStatus::Completed;
            $run->completed_at = now();
            $run->current_node_id = null;
            if (!$alreadyApplied && ($rewardResult['applied'] || $pointsResult['points'] > 0)) {
                $run->rewards_applied_at = now();
            }
            $run->save();

            $messages = array_filter([
                ...($rewardResult['messages'] ?? []),
                $pointsResult['message'] ?? null,
                'Boss derrotado. Mision completada.',
            ]);

            return ['status' => 'won', 'message' => implode(' ', $messages)];
        });

        if (!empty($result['message'])) {
            if ($result['status'] === 'error') {
                return back()->withErrors([$result['message']]);
            }

            return back()->with('status', $result['message']);
        }

        return back();
    }

    private function resolveCombat(CharacterMissionRun $run, ?FinalBoss $boss, array $tier): array
    {
        if (!$boss) {
            return [
                'resolvable' => false,
                'win' => false,
                'message' => 'La mision no tiene boss final.',
            ];
        }

        $character = $run->character()->first();
        if (!$character) {
            return [
                'resolvable' => false,
                'win' => false,
                'message' => 'No se pudo cargar el personaje.',
            ];
        }

        $stats = $character->effectiveStats();
        if (empty($stats)) {
            $stats = is_array($character->stats_json) ? $character->stats_json : [];
        }

        $hp = $stats['hp'] ?? null;
        $strength = $stats['strength'] ?? null;
        $magic = $stats['magic'] ?? null;
        $defense = $stats['defense'] ?? null;
        $speed = $stats['speed'] ?? null;

        if ($hp === null || $strength === null || $magic === null || $defense === null || $speed === null) {
            return [
                'resolvable' => false,
                'win' => false,
                'message' => 'No se puede resolver el combate: faltan stats del personaje.',
            ];
        }

        $bossStats = is_array($boss->base_stats_json) ? $boss->base_stats_json : [];
        $bossHp = $bossStats['hp'] ?? null;
        $bossDamage = $bossStats['damage'] ?? null;
        $bossDefense = $bossStats['defense'] ?? null;

        if ($bossHp === null || $bossDamage === null || $bossDefense === null) {
            return [
                'resolvable' => false,
                'win' => false,
                'message' => 'No se puede resolver el combate: faltan stats del boss.',
            ];
        }

        $woundMultiplier = max(0, 1 - (0.02 * (int) $run->wound_stacks));
        $damage = ($strength + $magic) * $woundMultiplier;
        $hp = $hp * $woundMultiplier;

        $characterPower = $hp + $damage + $defense + $speed;

        $bossMultiplier = (float) ($tier['boss_multiplier'] ?? 1.0);
        $bossPower = ($bossHp * $bossMultiplier) + ($bossDamage * $bossMultiplier) + ($bossDefense * $bossMultiplier);

        return [
            'resolvable' => true,
            'win' => $characterPower >= $bossPower,
            'message' => null,
            'character_power' => round($characterPower, 2),
            'boss_power' => round($bossPower, 2),
            'boss_multiplier' => $bossMultiplier,
            'wound_multiplier' => round($woundMultiplier, 2),
        ];
    }
}
