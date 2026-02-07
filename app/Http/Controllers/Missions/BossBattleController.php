<?php

declare(strict_types=1);

namespace App\Http\Controllers\Missions;

use App\Enums\BattleStatus;
use App\Enums\MissionRunStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Missions\SubmitBossActionRequest;
use App\Models\Battle;
use App\Models\BattleTurn;
use App\Models\CharacterMissionRun;
use App\Services\Combat\CombatLogFormatter;
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
    public function show(Request $request, CharacterMissionRun $run, MissionDifficultyService $difficulty): View|RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $battleResult = $this->getOrCreateBattle($run, $difficulty);
        if ($battleResult['error']) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors([$battleResult['error']]);
        }

        $battle = $battleResult['battle'];
        $turns = $battle->turns()
            ->orderByDesc('turn_number')
            ->take(6)
            ->get()
            ->reverse();

        $mission = $run->mission()->with('finalBoss')->first();

        return view('missions.boss_battle', [
            'run' => $run,
            'battle' => $battle,
            'mission' => $mission,
            'turns' => $turns,
        ]);
    }

    public function submit(
        SubmitBossActionRequest $request,
        CharacterMissionRun $run,
        MissionDifficultyService $difficulty,
        MissionRewardService $rewards,
        RacePointsService $points
    ): RedirectResponse {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $battleResult = $this->getOrCreateBattle($run, $difficulty);
        if ($battleResult['error']) {
            return redirect()
                ->route('game.missions.run', $run)
                ->withErrors([$battleResult['error']]);
        }

        $battle = $battleResult['battle'];
        $action = $request->validated('action');

        try {
            $resolved = false;
            DB::transaction(function () use ($battle, $run, $action, $difficulty, $rewards, $points, &$resolved): void {
                $lockedRun = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();
                $lockedBattle = Battle::query()->whereKey($battle->id)->lockForUpdate()->firstOrFail();

                if ($lockedBattle->status !== BattleStatus::Active) {
                    throw new \RuntimeException('battle_not_active');
                }

                if ($lockedRun->status !== MissionRunStatus::BossPending) {
                    throw new \RuntimeException('run_not_pending');
                }

                if ($lockedBattle->pending_p1_action) {
                    throw new \RuntimeException('already_submitted');
                }

                $lockedBattle->pending_p1_action = $action;

                $engine = new PveBossEngine();
                $formatter = new CombatLogFormatter();

                $turnNumber = (int) $lockedBattle->turn_number;
                $bossAction = $engine->bossActionForTurn($turnNumber);
                $lockedBattle->pending_p2_action = $bossAction;

                $resolution = $engine->resolveTurn($lockedBattle, $action, $bossAction);

                $notes = $formatter->formatTurn(
                    $turnNumber,
                    $action,
                    $bossAction,
                    $resolution['first_actor'],
                    $resolution['damage_to_p1'],
                    $resolution['damage_to_p2'],
                    $resolution['p1_hp'],
                    $resolution['p2_hp'],
                    $resolution['second_skipped']
                );

                BattleTurn::create([
                    'battle_id' => $lockedBattle->id,
                    'turn_number' => $turnNumber,
                    'p1_action' => $action,
                    'p2_action' => $bossAction,
                    'first_actor' => 'p1',
                    'damage_to_p1' => $resolution['damage_to_p1'],
                    'damage_to_p2' => $resolution['damage_to_p2'],
                    'notes_json' => $notes,
                ]);

                $lockedBattle->update([
                    'p1_hp' => $resolution['p1_hp'],
                    'p2_hp' => $resolution['p2_hp'],
                    'p1_defending' => false,
                    'p2_defending' => false,
                    'pending_p1_action' => null,
                    'pending_p2_action' => null,
                    'turn_number' => $turnNumber + 1,
                ]);

                if ($resolution['result']) {
                    $lockedBattle->update([
                        'status' => BattleStatus::Finished,
                        'result' => $resolution['result'],
                        'finished_at' => now(),
                    ]);

                    if ($resolution['result'] === 'p1_win') {
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
                    } else {
                        $lockedRun->status = MissionRunStatus::Failed;
                        $lockedRun->completed_at = now();
                        $lockedRun->current_node_id = null;
                        $lockedRun->save();
                    }
                }

                $resolved = true;
            });
        } catch (\RuntimeException $exception) {
            $message = match ($exception->getMessage()) {
                'already_submitted' => 'Ya enviaste tu accion. Espera el resultado.',
                'run_not_pending' => 'El combate no esta disponible para esta mision.',
                default => 'No se pudo registrar la accion. Intenta de nuevo.',
            };

            return redirect()
                ->route('game.missions.boss.show', $run)
                ->withErrors([$message]);
        }

        return redirect()
            ->route('game.missions.boss.show', $run)
            ->with('status', $resolved ? 'Turno resuelto.' : 'Accion enviada.');
    }

    /**
     * @return array{battle: Battle|null, error: string|null}
     */
    private function getOrCreateBattle(CharacterMissionRun $run, MissionDifficultyService $difficulty): array
    {
        $existing = Battle::query()
            ->where('mission_run_id', $run->id)
            ->where('type', 'pve')
            ->latest('id')
            ->first();

        if ($existing) {
            return ['battle' => $existing, 'error' => null];
        }

        if ($run->status !== MissionRunStatus::BossPending) {
            return ['battle' => null, 'error' => 'La mision no esta lista para el boss.'];
        }

        return DB::transaction(function () use ($run, $difficulty) {
            $lockedRun = CharacterMissionRun::query()->whereKey($run->id)->lockForUpdate()->firstOrFail();

            $existing = Battle::query()
                ->where('mission_run_id', $lockedRun->id)
                ->where('type', 'pve')
                ->latest('id')
                ->first();

            if ($existing) {
                return ['battle' => $existing, 'error' => null];
            }

            if ($lockedRun->status !== MissionRunStatus::BossPending) {
                return ['battle' => null, 'error' => 'La mision no esta lista para el boss.'];
            }

            $mission = $lockedRun->mission()->with('finalBoss')->firstOrFail();
            $boss = $mission->finalBoss;

            if (!$boss) {
                return ['battle' => null, 'error' => 'La mision no tiene boss final.'];
            }

            $bossStats = is_array($boss->base_stats_json) ? $boss->base_stats_json : [];
            $bossHp = $bossStats['hp'] ?? null;
            $bossAttack = $bossStats['attack'] ?? null;
            $bossDefense = $bossStats['defense'] ?? null;

            if ($bossHp === null || $bossAttack === null || $bossDefense === null) {
                return ['battle' => null, 'error' => 'El boss no tiene stats completos (hp/attack/defense).'];
            }

            $character = $lockedRun->character()->first();
            if (!$character) {
                return ['battle' => null, 'error' => 'No se pudo cargar el personaje.'];
            }

            $playerStats = $this->snapshotPlayerStats($character);
            if ($playerStats === null) {
                return ['battle' => null, 'error' => 'No se pudo cargar los stats del personaje.'];
            }

            $tier = $difficulty->resolveTier((int) $lockedRun->danger_score);
            $bossMultiplier = (float) ($tier['boss_multiplier'] ?? 1.0);

            $scaledHp = max(1, (int) floor(((float) $bossHp) * $bossMultiplier));
            $scaledAttack = max(1, (int) floor(((float) $bossAttack) * $bossMultiplier));
            $scaledDefense = max(0, (int) floor(((float) $bossDefense) * $bossMultiplier));

            $battle = Battle::create([
                'room_id' => null,
                'type' => 'pve',
                'status' => BattleStatus::Active,
                'player1_character_id' => $character->id,
                'player2_character_id' => null,
                'final_boss_id' => $boss->id,
                'mission_run_id' => $lockedRun->id,
                'turn_number' => 1,
                'p1_hp' => $playerStats['hp'],
                'p2_hp' => $scaledHp,
                'p1_defending' => false,
                'p2_defending' => false,
                'stats_p1_json' => $playerStats,
                'stats_p2_json' => [
                    'hp' => $scaledHp,
                    'attack' => $scaledAttack,
                    'defense' => $scaledDefense,
                    'speed' => 0,
                    'magic' => 0,
                ],
            ]);

            return ['battle' => $battle, 'error' => null];
        });
    }

    private function snapshotPlayerStats($character): ?array
    {
        $stats = $character->effectiveStats();

        $attack = $stats['fuerza'] ?? $stats['strength'] ?? null;
        $magic = $stats['magia'] ?? $stats['magic'] ?? null;
        $defense = $stats['defensa'] ?? $stats['defense'] ?? null;
        $speed = $stats['velocidad'] ?? $stats['speed'] ?? null;

        $hp = $character->hp_current ?? $character->hp_max ?? null;

        if ($hp === null || $attack === null || $magic === null || $defense === null || $speed === null) {
            return null;
        }

        return [
            'hp' => max(1, (int) $hp),
            'attack' => (int) $attack,
            'defense' => (int) $defense,
            'speed' => (int) $speed,
            'magic' => (int) $magic,
        ];
    }
}
