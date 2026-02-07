<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MissionChoiceStoreRequest;
use App\Http\Requests\Admin\MissionChoiceUpdateRequest;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;

class MissionChoiceController extends Controller
{
    public function index(Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);

        $choices = $node->choices()
            ->with('nextNode')
            ->orderBy('order')
            ->get();

        return view('admin.missions.choices.index', [
            'mission' => $mission,
            'node' => $node,
            'choices' => $choices,
            'stepIndex' => (int) $node->step_index,
        ]);
    }

    public function create(Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);
        $this->assertChoiceLimit($node);

        return view('admin.missions.choices.create', [
            'mission' => $mission,
            'node' => $node,
            'nextNodes' => $this->getNextNodes($mission, $node),
            'stepIndex' => (int) $node->step_index,
            'orderOptions' => range(1, 4),
        ]);
    }

    public function store(MissionChoiceStoreRequest $request, Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);
        $this->assertChoiceLimit($node);

        $payload = $this->buildPayload($request->validated(), $node);
        $payload['mission_node_id'] = $node->id;

        MissionChoice::create($payload);

        return redirect()
            ->route('missions.nodes.choices.index', [$mission, $node])
            ->with('status', 'Opcion creada correctamente.');
    }

    public function edit(Mission $mission, MissionNode $node, MissionChoice $choice)
    {
        $this->assertNodeBelongsToMission($mission, $node);
        $this->assertChoiceBelongsToNode($node, $choice);

        return view('admin.missions.choices.edit', [
            'mission' => $mission,
            'node' => $node,
            'choice' => $choice,
            'nextNodes' => $this->getNextNodes($mission, $node),
            'stepIndex' => (int) $node->step_index,
            'orderOptions' => range(1, 4),
        ]);
    }

    public function update(MissionChoiceUpdateRequest $request, Mission $mission, MissionNode $node, MissionChoice $choice)
    {
        $this->assertNodeBelongsToMission($mission, $node);
        $this->assertChoiceBelongsToNode($node, $choice);

        $payload = $this->buildPayload($request->validated(), $node);

        $choice->update($payload);

        return redirect()
            ->route('missions.nodes.choices.index', [$mission, $node])
            ->with('status', 'Opcion actualizada correctamente.');
    }

    public function destroy(Mission $mission, MissionNode $node, MissionChoice $choice)
    {
        $this->assertNodeBelongsToMission($mission, $node);
        $this->assertChoiceBelongsToNode($node, $choice);

        $remaining = $node->choices()->count();
        if ($remaining <= 3) {
            return redirect()
                ->route('missions.nodes.choices.index', [$mission, $node])
                ->withErrors(['choice' => 'Cada nodo debe tener al menos 3 opciones.']);
        }

        $choice->delete();

        return redirect()
            ->route('missions.nodes.choices.index', [$mission, $node])
            ->with('status', 'Opcion eliminada correctamente.');
    }

    private function buildPayload(array $validated, MissionNode $node): array
    {
        $effects = null;
        if (!empty($validated['effects_json_raw'])) {
            $decoded = json_decode($validated['effects_json_raw'], true);
            $effects = is_array($decoded) ? $decoded : null;
        }

        $payload = [
            'choice_text' => $validated['choice_text'],
            'outcome_text' => $validated['outcome_text'] ?? null,
            'difficulty_points' => (int) $validated['difficulty_points'],
            'order' => (int) $validated['order'],
            'effects_json' => $effects,
        ];

        if ((int) $node->step_index >= 6) {
            $payload['goes_to_boss'] = true;
            $payload['next_node_id'] = null;
        } else {
            $payload['goes_to_boss'] = false;
            $payload['next_node_id'] = (int) $validated['next_node_id'];
        }

        return $payload;
    }

    private function getNextNodes(Mission $mission, MissionNode $node)
    {
        if ((int) $node->step_index >= 6) {
            return collect();
        }

        return $mission->nodes()
            ->where('step_index', (int) $node->step_index + 1)
            ->orderBy('id')
            ->get();
    }

    private function assertNodeBelongsToMission(Mission $mission, MissionNode $node): void
    {
        if ($node->mission_id !== $mission->id) {
            abort(404);
        }
    }

    private function assertChoiceBelongsToNode(MissionNode $node, MissionChoice $choice): void
    {
        if ($choice->mission_node_id !== $node->id) {
            abort(404);
        }
    }

    private function assertChoiceLimit(MissionNode $node): void
    {
        if ($node->choices()->count() >= 4) {
            abort(403, 'Este nodo ya tiene 4 opciones.');
        }
    }
}
