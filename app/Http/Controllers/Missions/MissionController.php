<?php

namespace App\Http\Controllers\Missions;

use App\Enums\MissionRunStatus;
use App\Enums\MissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\CharacterMissionRun;
use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index(Request $request)
    {
        $character = $this->resolveCharacter($request);

        $missions = Mission::query()
            ->where('status', MissionStatus::Published)
            ->with('finalBoss')
            ->orderBy('title')
            ->get();

        $activeRun = $this->activeRunFor($character);

        return view('missions.index', [
            'missions' => $missions,
            'activeRun' => $activeRun,
        ]);
    }

    public function show(Request $request, Mission $mission)
    {
        if ($mission->status !== MissionStatus::Published) {
            abort(404);
        }

        $character = $this->resolveCharacter($request);
        $activeRun = $this->activeRunFor($character);

        return view('missions.show', [
            'mission' => $mission->load('finalBoss'),
            'activeRun' => $activeRun,
        ]);
    }

    private function resolveCharacter(Request $request): Character
    {
        $user = $request->user();
        $character = $user?->character;

        if (!$character) {
            abort(403);
        }

        return $character;
    }

    private function activeRunFor(Character $character): ?CharacterMissionRun
    {
        return CharacterMissionRun::query()
            ->where('character_id', $character->id)
            ->whereIn('status', [MissionRunStatus::Active, MissionRunStatus::BossPending])
            ->with('mission')
            ->first();
    }
}
