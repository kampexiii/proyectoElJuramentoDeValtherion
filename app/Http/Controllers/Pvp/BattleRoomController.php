<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pvp;

use App\Enums\BattleRoomStatus;
use App\Enums\BattleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pvp\CloseRoomRequest;
use App\Http\Requests\Pvp\CreateRoomRequest;
use App\Http\Requests\Pvp\JoinRoomRequest;
use App\Models\Battle;
use App\Models\BattleRoom;
use App\Models\Character;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BattleRoomController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeRoom = $this->activeRoomFor($user->id);

        $rooms = BattleRoom::query()
            ->where('status', BattleRoomStatus::Open)
            ->whereNull('guest_user_id')
            ->with('owner')
            ->orderBy('created_at')
            ->get();

        return view('pvp.lobby', [
            'rooms' => $rooms,
            'activeRoom' => $activeRoom,
        ]);
    }

    public function store(CreateRoomRequest $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->character) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'Necesitas un personaje para crear una sala.');
        }

        $activeRoom = $this->activeRoomFor($user->id);
        if ($activeRoom) {
            return redirect()
                ->route('pvp.rooms.show', $activeRoom)
                ->with('error', 'Ya estas en una sala activa. Cierra esa sala para crear otra.');
        }

        $room = BattleRoom::create([
            'owner_user_id' => $user->id,
            'status' => BattleRoomStatus::Open,
        ]);

        return redirect()
            ->route('pvp.rooms.show', $room)
            ->with('success', 'Sala creada. Esperando rival.');
    }

    public function show(Request $request, BattleRoom $room): View|RedirectResponse
    {
        $user = $request->user();
        if (!$this->userInRoom($room, $user->id)) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'No perteneces a esta sala.');
        }

        $room->load(['owner', 'guest', 'battle']);

        return view('pvp.room', [
            'room' => $room,
        ]);
    }

    public function join(JoinRoomRequest $request, BattleRoom $room): RedirectResponse
    {
        $user = $request->user();
        if (!$user->character) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'Necesitas un personaje para unirte a una sala.');
        }

        $activeRoom = $this->activeRoomFor($user->id);
        if ($activeRoom && $activeRoom->id !== $room->id) {
            return redirect()
                ->route('pvp.rooms.show', $activeRoom)
                ->with('error', 'Ya estas en una sala activa. Debes cerrarla para unirte a otra.');
        }

        if ($room->owner_user_id === $user->id) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'Ya eres el owner de esta sala.');
        }

        if ($room->guest_user_id === $user->id) {
            return redirect()->route('pvp.rooms.show', $room);
        }

        try {
            DB::transaction(function () use ($room, $user): void {
                $lockedRoom = BattleRoom::query()
                    ->whereKey($room->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedRoom || $lockedRoom->status !== BattleRoomStatus::Open || $lockedRoom->guest_user_id) {
                    throw new \RuntimeException('room_not_open');
                }

                $ownerCharacter = $lockedRoom->owner?->character;
                if (!$ownerCharacter) {
                    throw new \RuntimeException('owner_no_character');
                }

                $guestCharacter = $user->character;
                if (!$guestCharacter) {
                    throw new \RuntimeException('guest_no_character');
                }

                $p1Stats = $this->snapshotStats($ownerCharacter);
                $p2Stats = $this->snapshotStats($guestCharacter);

                $battle = Battle::create([
                    'room_id' => $lockedRoom->id,
                    'type' => 'pvp',
                    'status' => BattleStatus::Active,
                    'player1_character_id' => $ownerCharacter->id,
                    'player2_character_id' => $guestCharacter->id,
                    'turn_number' => 1,
                    'p1_hp' => $p1Stats['hp'],
                    'p2_hp' => $p2Stats['hp'],
                    'p1_defending' => false,
                    'p2_defending' => false,
                    'stats_p1_json' => $p1Stats,
                    'stats_p2_json' => $p2Stats,
                ]);

                $lockedRoom->update([
                    'guest_user_id' => $user->id,
                    'status' => BattleRoomStatus::InProgress,
                    'battle_id' => $battle->id,
                ]);
            });
        } catch (\RuntimeException $exception) {
            $message = match ($exception->getMessage()) {
                'owner_no_character' => 'El owner no tiene personaje disponible. La sala no puede iniciar.',
                'guest_no_character' => 'Necesitas un personaje para unirte a la sala.',
                default => 'La sala ya no esta disponible para unirse.',
            };

            return redirect()
                ->route('pvp.lobby')
                ->with('error', $message);
        }

        return redirect()
            ->route('pvp.rooms.show', $room)
            ->with('success', 'Te uniste a la sala. La batalla ha comenzado.');
    }

    public function battle(Request $request, BattleRoom $room): View|RedirectResponse
    {
        $user = $request->user();
        if (!$this->userInRoom($room, $user->id)) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'No perteneces a esta sala.');
        }

        if ($room->status !== BattleRoomStatus::InProgress) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'La batalla aun no esta disponible.');
        }

        return view('game.peleas');
    }

    public function close(CloseRoomRequest $request, BattleRoom $room): RedirectResponse
    {
        $user = $request->user();
        if (!$this->userInRoom($room, $user->id)) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'No perteneces a esta sala.');
        }

        if ($room->status === BattleRoomStatus::Closed) {
            return redirect()
                ->route('pvp.lobby')
                ->with('error', 'La sala ya fue cerrada.');
        }

        $canCancelOpen = $room->status === BattleRoomStatus::Open
            && $room->guest_user_id === null
            && $room->owner_user_id === $user->id;

        $canCloseFinished = $room->status === BattleRoomStatus::Finished;

        if (!$canCancelOpen && !$canCloseFinished) {
            return redirect()
                ->route('pvp.rooms.show', $room)
                ->with('error', 'No puedes cerrar la sala en este estado.');
        }

        $room->update([
            'status' => BattleRoomStatus::Closed,
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('pvp.lobby')
            ->with('success', 'Sala cerrada.');
    }

    private function activeRoomFor(int $userId): ?BattleRoom
    {
        return BattleRoom::query()
            ->active()
            ->where(function ($query) use ($userId) {
                $query->where('owner_user_id', $userId)
                    ->orWhere('guest_user_id', $userId);
            })
            ->first();
    }

    private function userInRoom(BattleRoom $room, int $userId): bool
    {
        return $room->owner_user_id === $userId || $room->guest_user_id === $userId;
    }

    private function snapshotStats(Character $character): array
    {
        $stats = $character->effectiveStats();
        $hp = (int) ($character->hp_current ?? $character->hp_max ?? 1);

        return [
            'hp' => max(1, $hp),
            'attack' => (int) ($stats['fuerza'] ?? 0),
            'defense' => (int) ($stats['defensa'] ?? 0),
            'speed' => (int) ($stats['velocidad'] ?? 0),
            'magic' => (int) ($stats['magia'] ?? 0),
        ];
    }
}
