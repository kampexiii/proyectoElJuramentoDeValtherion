<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MissionNodeStoreRequest;
use App\Http\Requests\Admin\MissionNodeUpdateRequest;
use App\Models\Mission;
use App\Models\MissionNode;

class MissionNodeController extends Controller
{
    public function index(Mission $mission)
    {
        $nodes = $mission->nodes()
            ->withCount('choices')
            ->orderBy('step_index')
            ->orderBy('id')
            ->get()
            ->groupBy('step_index');

        return view('admin.missions.nodes.index', [
            'mission' => $mission,
            'nodesByStep' => $nodes,
            'steps' => range(1, 6),
        ]);
    }

    public function create(Mission $mission)
    {
        return view('admin.missions.nodes.create', [
            'mission' => $mission,
            'steps' => range(1, 6),
        ]);
    }

    public function store(MissionNodeStoreRequest $request, Mission $mission)
    {
        $payload = $request->validated();
        $payload['mission_id'] = $mission->id;

        MissionNode::create($payload);

        return redirect()
            ->route('missions.nodes.index', $mission)
            ->with('status', 'Nodo creado correctamente.');
    }

    public function edit(Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);

        return view('admin.missions.nodes.edit', [
            'mission' => $mission,
            'node' => $node,
            'steps' => range(1, 6),
        ]);
    }

    public function update(MissionNodeUpdateRequest $request, Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);

        $node->update($request->validated());

        return redirect()
            ->route('missions.nodes.index', $mission)
            ->with('status', 'Nodo actualizado correctamente.');
    }

    public function destroy(Mission $mission, MissionNode $node)
    {
        $this->assertNodeBelongsToMission($mission, $node);

        $node->delete();

        return redirect()
            ->route('missions.nodes.index', $mission)
            ->with('status', 'Nodo eliminado correctamente.');
    }

    private function assertNodeBelongsToMission(Mission $mission, MissionNode $node): void
    {
        if ($node->mission_id !== $mission->id) {
            abort(404);
        }
    }
}
