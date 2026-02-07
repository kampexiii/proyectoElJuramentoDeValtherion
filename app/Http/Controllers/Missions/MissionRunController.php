<?php

namespace App\Http\Controllers\Missions;

use App\Enums\MissionRunStatus;
use App\Enums\MissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Missions\AbandonMissionRunRequest;
use App\Http\Requests\Missions\ChooseMissionOptionRequest;
use App\Http\Requests\Missions\StartMissionRunRequest;
use App\Models\CharacterMissionRun;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Services\Missions\MissionRunService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MissionRunController extends Controller
{
    public function show(Request $request, CharacterMissionRun $run)
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $mission = $run->mission()->firstOrFail();
        $node = null;
        $choices = collect();

        if ($run->status === MissionRunStatus::Active && $run->current_node_id) {
            $node = MissionNode::query()->where('id', $run->current_node_id)->first();
            if ($node) {
                $choices = $node->choices()->orderBy('order')->get();
            }
        }

        return view('missions.run', [
            'mission' => $mission,
            'run' => $run,
            'node' => $node,
            'choices' => $choices,
        ]);
    }

    public function start(StartMissionRunRequest $request, Mission $mission, MissionRunService $service): RedirectResponse
    {
        if ($mission->status !== MissionStatus::Published) {
            abort(404);
        }

        $character = $request->user()?->character;
        if (!$character) {
            abort(403);
        }

        try {
            $run = $service->start($mission, $character);
        } catch (\RuntimeException $exception) {
            return back()->withErrors([$exception->getMessage()]);
        }

        return redirect()->route('game.missions.run', $run);
    }

    public function choose(ChooseMissionOptionRequest $request, CharacterMissionRun $run, MissionRunService $service): RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        if ($run->status !== MissionRunStatus::Active) {
            return back()->withErrors(['La mision no esta activa.']);
        }

        $validated = $request->validated();
        $choiceId = (int) $validated['choice_id'];
        $choice = MissionChoice::query()->where('id', $choiceId)->first();
        if (!$choice) {
            return back()->withErrors(['Opcion no valida.']);
        }

        try {
            $service->choose($run, $choice);
        } catch (\RuntimeException $exception) {
            return back()->withErrors([$exception->getMessage()]);
        }

        return redirect()->route('game.missions.run', $run);
    }

    public function abandon(AbandonMissionRunRequest $request, CharacterMissionRun $run, MissionRunService $service): RedirectResponse
    {
        $character = $request->user()?->character;
        if (!$character || $run->character_id !== $character->id) {
            abort(404);
        }

        $usePartial = $request->boolean('partial');

        if (!$usePartial) {
            if ($run->status === MissionRunStatus::BossPending) {
                return back()->withErrors(['Debes enfrentar al boss o retirarte con XP parcial.']);
            }
            $service->abandon($run);

            return redirect()->route('game.missions.index')->with('status', 'Mision abandonada.');
        }

        try {
            $result = $service->abandonWithPartialXp($run, $character);
        } catch (\RuntimeException $exception) {
            return back()->withErrors([$exception->getMessage()]);
        }

        $partialXp = (int) ($result['partial_xp'] ?? 0);

        return redirect()
            ->route('game.missions.index')
            ->with('status', "Has abandonado la mision. XP obtenida: {$partialXp} (10%). Sin oro/objetos/puntos de raza.");
    }
}
