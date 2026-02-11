<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pvp;

use App\Enums\BattleRoomStatus;
use App\Enums\BattleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pvp\SubmitActionRequest;
use App\Models\Battle;
use App\Models\BattleRoom;
use App\Models\BattleTurn;
use App\Services\Combat\CombatLogFormatter;
use App\Services\Combat\PvpCombatEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BattleController extends Controller
{
    public function show(Request $request, BattleRoom $room): View|RedirectResponse
    {
        $user = $request->user();
        if (!$this->userInRoom($room, $user->id)) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'No perteneces a esta sala.');
        }

        $room->load(['battle', 'owner', 'guest']);
        if (!$room->battle) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'La batalla aun no esta lista.');
        }

        $battle = $room->battle;
        $battle->loadMissing(['player1Character.race', 'player2Character.race']);
        $playerSide = $this->resolvePlayerSide($request, $battle);
        if ($playerSide === null) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'No tienes personaje en esta batalla.');
        }

        $turns = $battle->turns()
            ->orderByDesc('turn_number')
            ->take(6)
            ->get()
            ->reverse();

        $myPending = $playerSide === 'p1' ? $battle->pending_p1_action : $battle->pending_p2_action;
        $theirPending = $playerSide === 'p1' ? $battle->pending_p2_action : $battle->pending_p1_action;

        return view('pvp.battle', [
            'room' => $room,
            'battle' => $battle,
            'playerSide' => $playerSide,
            'myPending' => $myPending,
            'theirPending' => $theirPending,
            'turns' => $turns,
        ]);
    }

    public function submit(SubmitActionRequest $request, BattleRoom $room): RedirectResponse
    {
        $user = $request->user();
        if (!$this->userInRoom($room, $user->id)) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'No perteneces a esta sala.');
        }

        $room->load('battle');
        if (!$room->battle) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'La batalla aun no esta lista.');
        }

        if ($room->status !== BattleRoomStatus::InProgress) {
            if ($room->status === BattleRoomStatus::Closed) {
                return redirect()
                    ->route('pvp.lobby')
                    ->with('success', 'La sala fue cerrada por el anfitrion.');
            }
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'La sala no esta en combate.');
        }

        $battle = $room->battle;
        if ($battle->status !== BattleStatus::Active) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'La batalla ya finalizo.');
        }

        $playerSide = $this->resolvePlayerSide($request, $battle);
        if ($playerSide === null) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'No tienes personaje en esta batalla.');
        }

        $action = $request->validated('action');

        try {
            $resolved = false;
            DB::transaction(function () use ($battle, $room, $playerSide, $action, &$resolved): void {
                $lockedBattle = Battle::query()
                    ->whereKey($battle->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedBattle || $lockedBattle->status !== BattleStatus::Active) {
                    throw new \RuntimeException('battle_not_active');
                }

                if ($playerSide === 'p1') {
                    if ($lockedBattle->pending_p1_action) {
                        throw new \RuntimeException('already_submitted');
                    }
                    $lockedBattle->pending_p1_action = $action;
                } else {
                    if ($lockedBattle->pending_p2_action) {
                        throw new \RuntimeException('already_submitted');
                    }
                    $lockedBattle->pending_p2_action = $action;
                }

                $lockedBattle->save();

                if ($lockedBattle->pending_p1_action && $lockedBattle->pending_p2_action) {
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

                    $resolved = true;
                }
            });
        } catch (\RuntimeException $exception) {
            $message = match ($exception->getMessage()) {
                'already_submitted' => 'Ya enviaste tu accion. Espera a tu rival.',
                default => 'No se pudo registrar la accion. Intenta de nuevo.',
            };

            return redirect()
                ->route('pvp.rooms.battle', $room)
                ->with('error', $message);
        }

        $success = $resolved
            ? 'Turno resuelto.'
            : 'Accion enviada. Esperando rival.';

        return redirect()
            ->route('pvp.rooms.battle', $room)
            ->with('success', $success);
    }

    private function userInRoom(BattleRoom $room, int $userId): bool
    {
        return $room->owner_user_id === $userId || $room->guest_user_id === $userId;
    }

    private function resolvePlayerSide(Request $request, Battle $battle): ?string
    {
        $character = $request->user()->character;
        if (!$character) {
            return null;
        }

        if ((int) $battle->player1_character_id === (int) $character->id) {
            return 'p1';
        }

        if ((int) $battle->player2_character_id === (int) $character->id) {
            return 'p2';
        }

        return null;
    }
}
