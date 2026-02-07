<?php

declare(strict_types=1);

namespace App\Http\Controllers\Missions;

use App\Enums\MissionRunStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Missions\SubmitBossActionRequest;
use App\Models\CharacterMissionRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BossBattleController extends Controller
{
    public function show(Request $request, CharacterMissionRun $run): View|RedirectResponse
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

        $mission = $run->mission()->with('finalBoss')->first();

        return view('missions.boss_battle', [
            'run' => $run,
            'mission' => $mission,
        ]);
    }

    public function action(SubmitBossActionRequest $request, CharacterMissionRun $run): RedirectResponse
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

        $request->validated();

        return back()->with('status', 'Accion registrada (motor en construccion).');
    }
}
