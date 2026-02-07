<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MissionStoreRequest;
use App\Http\Requests\Admin\MissionUpdateRequest;
use App\Models\FinalBoss;
use App\Models\Item;
use App\Models\Mission;
use App\Models\MissionReward;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class MissionController extends Controller
{
    public function index()
    {
        $missions = Mission::query()
            ->with('finalBoss')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.missions.index', [
            'missions' => $missions,
        ]);
    }

    public function create()
    {
        $hasItemsTable = Schema::hasTable('items');

        return view('admin.missions.create', [
            'bosses' => FinalBoss::query()->orderBy('name')->get(),
            'statusOptions' => $this->statusOptions(),
            'items' => $hasItemsTable ? Item::query()->orderBy('name')->get() : collect(),
            'hasItemsTable' => $hasItemsTable,
        ]);
    }

    public function store(MissionStoreRequest $request)
    {
        $validated = $request->validated();

        $mission = Mission::create(Arr::only($validated, [
            'title',
            'slug',
            'status',
            'repeatable',
            'base_race_points',
            'intro_text',
            'context_text',
            'final_boss_id',
        ]));

        $this->upsertReward($mission->id, $validated, $request);

        return redirect()
            ->route('missions.edit', $mission)
            ->with('status', 'Mision creada correctamente.');
    }

    public function edit(Mission $mission)
    {
        $hasItemsTable = Schema::hasTable('items');

        return view('admin.missions.edit', [
            'mission' => $mission->load('reward', 'finalBoss'),
            'bosses' => FinalBoss::query()->orderBy('name')->get(),
            'statusOptions' => $this->statusOptions(),
            'items' => $hasItemsTable ? Item::query()->orderBy('name')->get() : collect(),
            'hasItemsTable' => $hasItemsTable,
        ]);
    }

    public function update(MissionUpdateRequest $request, Mission $mission)
    {
        $validated = $request->validated();

        $mission->update(Arr::only($validated, [
            'title',
            'slug',
            'status',
            'repeatable',
            'base_race_points',
            'intro_text',
            'context_text',
            'final_boss_id',
        ]));

        $this->upsertReward($mission->id, $validated, $request);

        return redirect()
            ->route('missions.edit', $mission)
            ->with('status', 'Mision actualizada correctamente.');
    }

    public function destroy(Mission $mission)
    {
        $mission->delete();

        return redirect()
            ->route('missions.index')
            ->with('status', 'Mision eliminada correctamente.');
    }

    private function statusOptions(): array
    {
        return array_map(static fn (MissionStatus $status) => [
            'value' => $status->value,
            'label' => ucfirst($status->value),
        ], MissionStatus::cases());
    }

    private function upsertReward(int $missionId, array $validated, MissionStoreRequest|MissionUpdateRequest $request): void
    {
        $hasItemsTable = Schema::hasTable('items');
        $itemsPayload = $this->buildItemsPayload($request, $hasItemsTable);

        MissionReward::updateOrCreate(
            ['mission_id' => $missionId],
            [
                'xp' => (int) ($validated['xp'] ?? 0),
                'gold' => (int) ($validated['gold'] ?? 0),
                'items_json' => $itemsPayload,
            ]
        );
    }

    private function buildItemsPayload(MissionStoreRequest|MissionUpdateRequest $request, bool $hasItemsTable): ?array
    {
        if ($hasItemsTable) {
            $items = collect($request->input('items', []))
                ->filter(function ($row) {
                    return !empty($row['item_id']) && !empty($row['qty']);
                })
                ->map(function ($row) {
                    return [
                        'item_id' => (int) $row['item_id'],
                        'qty' => (int) $row['qty'],
                    ];
                })
                ->values()
                ->all();

            return empty($items) ? null : $items;
        }

        $raw = $request->input('items_json_raw');
        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
