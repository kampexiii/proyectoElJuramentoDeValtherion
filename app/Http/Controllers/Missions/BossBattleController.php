<?php

declare(strict_types=1);

namespace App\Http\Controllers\Missions;

use App\Enums\MissionRunStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Missions\SubmitBossActionRequest;
use App\Models\Battle;
use App\Models\BattleTurn;
use App\Models\CharacterMissionRun;
use App\Services\Combat\PveBossEngine;
use App\Services\Missions\MissionDifficultyService;
use App\Services\Missions\MissionRewardService;
use App\Services\Missions\RacePointsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BossBattleController extends Controller
{
    public function show(Request $request, CharacterMissionRun $run, PveBossEngine $engine, MissionDifficultyService $difficulty): View|RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        if ($run->status !== MissionRunStatus::BossPending) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors(['La mision no esta lista para el boss.']);
        }

        try {
            $battle = $engine->startBattleIfMissing($run);
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors([$exception->getMessage()]);
        }

        $turns = BattleTurn::query()
            ->where('battle_id', $battle->id)
            ->orderByDesc('turn_number')
            ->take(5)
            ->get()
            ->reverse();

        $mission = $run->mission()->with('finalBoss', 'reward')->first();
        $tier = $difficulty->resolveTier((int) $run->danger_score);

        return view('missions.boss_battle', [
            'run' => $run,
            'mission' => $mission,
            'battle' => $battle,
            'turns' => $turns,
            'tier' => $tier,
        ]);
    }

    public function action(
        SubmitBossActionRequest $request,
        CharacterMissionRun $run,
        PveBossEngine $engine,
        MissionDifficultyService $difficulty,
        MissionRewardService $rewards,
        RacePointsService $points
    ): RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        if ($run->status !== MissionRunStatus::BossPending) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors(['La mision no esta lista para el boss.']);
        }

        $action = (string) $request->validated('action');

        try {
            $battle = $engine->startBattleIfMissing($run);
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors([$exception->getMessage()]);
        }

        try {
            DB::transaction(function () use ($battle, $run, $action, $engine, $difficulty, $rewards, $points): void {
                $lockedRun = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();
                $lockedBattle = Battle::query()->whereKey($battle->id)->lockForUpdate()->firstOrFail();

                if ($lockedRun->status !== MissionRunStatus::BossPending) {
                    throw new \RuntimeException('run_not_pending');
                }

                $lockedBattle = $engine->resolveTurn($lockedBattle, $action);

                if ($lockedBattle->status->value === 'finished') {
                    if ($lockedBattle->result === 'p1_win') {
                        $mission = $lockedRun->mission()->with('reward')->firstOrFail();
                        $tier = $difficulty->resolveTier((int) $lockedRun->danger_score);

                        $alreadyApplied = $lockedRun->rewards_applied_at !== null;
                        if ($mission->reward) {
                            $rewardResult = $rewards->applyRewards(
                                $mission->reward,
                                $lockedRun->character_id,
                                $mission->reward->items_json,
                                $alreadyApplied
                            );
                        } else {
                            $rewardResult = ['applied' => false, 'messages' => ['No hay rewards configurados.']];
                        }

                        $pointsResult = $points->applyMissionPoints($lockedRun, $tier, $alreadyApplied);

                        $lockedRun->status = MissionRunStatus::Completed;
                        $lockedRun->completed_at = now();
                        $lockedRun->current_node_id = null;
                        if (!$alreadyApplied && ($rewardResult['applied'] || $pointsResult['points'] > 0)) {
                            $lockedRun->rewards_applied_at = now();
                        }
                        $lockedRun->save();
                    } elseif ($lockedBattle->result === 'p2_win') {
                        $lockedRun->status = MissionRunStatus::Failed;
                        $lockedRun->completed_at = now();
                        $lockedRun->current_node_id = null;
                        $lockedRun->save();
                    }
                }
            });
        } catch (\RuntimeException $exception) {
            $message = match ($exception->getMessage()) {
                'run_not_pending' => 'El combate no esta disponible para esta mision.',
                default => $exception->getMessage() ?: 'No se pudo registrar la accion.',
            };

            return redirect()
                ->route('game.missions.boss.show', $run)
                ->withErrors([$message]);
        }

        return redirect()
            ->route('game.missions.boss.show', $run)
            ->with('status', 'Turno resuelto.');
    }
}
