<?php

declare(strict_types=1);

namespace App\Http\Controllers\Battles;

use App\Enums\BattleRoomStatus;
use App\Enums\BattleStatus;
use App\Http\Controllers\Controller;
use App\Models\Battle;
use App\Models\BattleRoom;
use App\Models\BattleTurn;
use App\Models\CharacterMissionRun;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BattleRoomStateController extends Controller
{
    private const TURN_DURATION_SECONDS = 60;

    public function __invoke(Request $request, BattleRoom $room): JsonResponse
    {
        $user = $request->user();
        if (!$user || ($room->owner_user_id !== $user->id && $room->guest_user_id !== $user->id)) {
            abort(403);
        }

        $room->load('battle');
        $battle = $room->battle;
        if (!$battle) {
            return response()->json([
                'room_status' => $this->roomStatus($room),
                'battle_status' => 'waiting_actions',
                'turn_number' => 1,
                'state_version' => $this->stateVersion($room, null, null),
                'server_unix' => now()->timestamp,
                'turn_deadline_unix' => now()->timestamp + self::TURN_DURATION_SECONDS,
                'my_action_submitted' => false,
                'enemy_action_submitted' => false,
            ]);
        }

        $characterId = $user->character?->id;
        if (!$characterId) {
            abort(403);
        }

        $playerSide = $this->resolvePlayerSide($battle, (int) $characterId);
        if ($playerSide === null) {
            abort(403);
        }

        $lastTurn = BattleTurn::query()
            ->where('battle_id', $battle->id)
            ->latest('id')
            ->first();

        $turnStartedAt = $lastTurn?->created_at ?? $battle->updated_at ?? $battle->created_at;
        $deadlineUnix = ($turnStartedAt?->timestamp ?? now()->timestamp) + self::TURN_DURATION_SECONDS;

        $myPending = $playerSide === 'p1' ? $battle->pending_p1_action : $battle->pending_p2_action;
        $enemyPending = $playerSide === 'p1' ? $battle->pending_p2_action : $battle->pending_p1_action;

        return response()->json([
            'room_status' => $this->roomStatus($room),
            'battle_status' => $this->battleStatus($battle),
            'turn_number' => (int) $battle->turn_number,
            'state_version' => $this->stateVersion($room, $battle, $lastTurn),
            'server_unix' => now()->timestamp,
            'turn_deadline_unix' => $deadlineUnix,
            'my_action_submitted' => (bool) $myPending,
            'enemy_action_submitted' => (bool) $enemyPending,
        ]);
    }

    public function bossState(Request $request, CharacterMissionRun $run): JsonResponse
    {
        $character = $request->user()?->character;
        if (!$character || (int) $run->character_id !== (int) $character->id) {
            abort(403);
        }

        $battle = Battle::query()
            ->where('mission_run_id', $run->id)
            ->latest('id')
            ->first();

        if (!$battle) {
            return response()->json([
                'room_status' => 'active',
                'battle_status' => 'waiting_actions',
                'turn_number' => 1,
                'state_version' => 0,
                'server_unix' => now()->timestamp,
                'turn_deadline_unix' => now()->timestamp + self::TURN_DURATION_SECONDS,
                'my_action_submitted' => false,
                'enemy_action_submitted' => false,
            ]);
        }

        $lastTurn = BattleTurn::query()
            ->where('battle_id', $battle->id)
            ->latest('id')
            ->first();

        $turnStartedAt = $lastTurn?->created_at ?? $battle->updated_at ?? $battle->created_at;
        $deadlineUnix = ($turnStartedAt?->timestamp ?? now()->timestamp) + self::TURN_DURATION_SECONDS;

        return response()->json([
            'room_status' => $battle->status === BattleStatus::Finished ? 'finished' : 'active',
            'battle_status' => $battle->status === BattleStatus::Finished ? 'finished' : 'waiting_actions',
            'turn_number' => (int) $battle->turn_number,
            'state_version' => $this->stateVersion(null, $battle, $lastTurn),
            'server_unix' => now()->timestamp,
            'turn_deadline_unix' => $deadlineUnix,
            'my_action_submitted' => false,
            'enemy_action_submitted' => false,
        ]);
    }

    private function resolvePlayerSide(Battle $battle, int $characterId): ?string
    {
        if ((int) $battle->player1_character_id === $characterId) {
            return 'p1';
        }

        if ((int) $battle->player2_character_id === $characterId) {
            return 'p2';
        }

        return null;
    }

    private function roomStatus(?BattleRoom $room): string
    {
        if (!$room) {
            return 'active';
        }

        return match ($room->status) {
            BattleRoomStatus::Closed => 'closed',
            BattleRoomStatus::Finished => 'finished',
            default => 'active',
        };
    }

    private function battleStatus(Battle $battle): string
    {
        if ($battle->status === BattleStatus::Finished) {
            return 'finished';
        }

        if ($battle->pending_p1_action && $battle->pending_p2_action) {
            return 'resolving';
        }

        return 'waiting_actions';
    }

    private function stateVersion(?BattleRoom $room, ?Battle $battle, ?BattleTurn $lastTurn): int
    {
        $versions = [0];

        if ($room?->updated_at) {
            $versions[] = $this->timestampVersion($room->updated_at);
        }

        if ($battle?->updated_at) {
            $versions[] = $this->timestampVersion($battle->updated_at);
        }

        if ($lastTurn?->created_at) {
            $versions[] = $this->timestampVersion($lastTurn->created_at);
        }

        return (int) max($versions);
    }

    private function timestampVersion(CarbonInterface $timestamp): int
    {
        return (int) $timestamp->format('Uu');
    }
}
