<?php

declare(strict_types=1);

namespace App\Http\Controllers\Battles;

use App\Enums\BattleRoomStatus;
use App\Enums\BattleStatus;
use App\Http\Controllers\Controller;
use App\Models\Battle;
use App\Models\BattleRoom;
use App\Models\BattleTurn;
use App\Services\Combat\CombatLogFormatter;
use App\Services\Combat\PvpCombatEngine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BattleRoomAutoResolveController extends Controller
{
    private const TURN_DURATION_SECONDS = 60;

    public function __invoke(Request $request, BattleRoom $room): Response
    {
        $user = $request->user();
        if (!$user || ($room->owner_user_id !== $user->id && $room->guest_user_id !== $user->id)) {
            abort(403);
        }

        $room->load('battle');
        $battle = $room->battle;
        if (!$battle || $room->status !== BattleRoomStatus::InProgress) {
            return response()->noContent();
        }

        if ($battle->status !== BattleStatus::Active) {
            return response()->noContent();
        }

        $lastTurn = BattleTurn::query()
            ->where('battle_id', $battle->id)
            ->latest('id')
            ->first();

        $turnStartedAt = $lastTurn?->created_at ?? $battle->updated_at ?? $battle->created_at;
        $deadlineUnix = ($turnStartedAt?->timestamp ?? now()->timestamp) + self::TURN_DURATION_SECONDS;
        if (now()->timestamp < $deadlineUnix) {
            return response()->noContent();
        }

        DB::transaction(function () use ($battle, $room): void {
            $lockedBattle = Battle::query()
                ->whereKey($battle->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedBattle || $lockedBattle->status !== BattleStatus::Active) {
                return;
            }

            if ($lockedBattle->pending_p1_action && $lockedBattle->pending_p2_action) {
                return;
            }

            if (!$lockedBattle->pending_p1_action) {
                $lockedBattle->pending_p1_action = 'defend';
            }

            if (!$lockedBattle->pending_p2_action) {
                $lockedBattle->pending_p2_action = 'defend';
            }

            if (!$lockedBattle->pending_p1_action || !$lockedBattle->pending_p2_action) {
                return;
            }

            $engine = new PvpCombatEngine();
            $formatter = new CombatLogFormatter();

            $turnNumber = (int) $lockedBattle->turn_number;
            $resolution = $engine->resolveTurn(
                $lockedBattle,
                $lockedBattle->pending_p1_action,
                $lockedBattle->pending_p2_action
            );

            $notes = $formatter->formatTurn(
                $turnNumber,
                $lockedBattle->pending_p1_action,
                $lockedBattle->pending_p2_action,
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
                'p1_action' => $lockedBattle->pending_p1_action,
                'p2_action' => $lockedBattle->pending_p2_action,
                'first_actor' => $resolution['first_actor'],
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

                $room->update([
                    'status' => BattleRoomStatus::Finished,
                ]);
            }
        });

        return response()->noContent();
    }
}
