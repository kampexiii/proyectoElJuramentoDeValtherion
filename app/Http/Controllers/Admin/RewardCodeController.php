<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Mount;
use App\Models\RewardCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RewardCodeController extends Controller
{
    public function index()
    {
        [$optionMap, $optionGroups] = $this->buildRewardOptions();

        return view('admin.index', [
            'rewardOptionGroups' => $optionGroups,
            'rewardOptionsAvailable' => !empty($optionMap),
            'hasRewardTable' => Schema::hasTable('reward_codes'),
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('reward_codes')) {
            return back()->withErrors(['code' => 'La tabla de códigos aún no está creada.']);
        }

        [$optionMap] = $this->buildRewardOptions();
        if (empty($optionMap)) {
            return back()->withErrors(['reward' => 'No hay recompensas disponibles.']);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'reward' => ['required', 'string', Rule::in(array_keys($optionMap))],
        ]);

        $codeValue = Str::upper(trim($validated['code']));
        if (RewardCode::query()->whereRaw('upper(code) = ?', [$codeValue])->exists()) {
            return back()->withErrors(['code' => 'Ese código ya existe.'])->withInput();
        }

        $choice = $optionMap[$validated['reward']] ?? null;
        if (!$choice) {
            return back()->withErrors(['reward' => 'Recompensa no válida.'])->withInput();
        }

        RewardCode::create([
            'code' => $codeValue,
            'type' => $choice['type'],
            'payload_json' => $choice['payload'],
            'uses_max' => 1,
            'uses_count' => 0,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('code-created', 'Código creado correctamente.');
    }

    private function buildRewardOptions(): array
    {
        $optionMap = [];
        $optionGroups = [];

        $goldOptions = [
            ['value' => 'gold:100', 'label' => 'Oro +100', 'type' => 'gold', 'payload' => ['gold' => 100]],
            ['value' => 'gold:250', 'label' => 'Oro +250', 'type' => 'gold', 'payload' => ['gold' => 250]],
            ['value' => 'gold:500', 'label' => 'Oro +500', 'type' => 'gold', 'payload' => ['gold' => 500]],
        ];
        $optionGroups[] = [
            'label' => 'Oro',
            'options' => $goldOptions,
        ];

        foreach ($goldOptions as $option) {
            $optionMap[$option['value']] = [
                'type' => $option['type'],
                'payload' => $option['payload'],
            ];
        }

        if (Schema::hasTable('mounts')) {
            $mountOptions = Mount::query()
                ->orderBy('name')
                ->get()
                ->map(function ($mount) {
                    return [
                        'value' => 'mount:' . $mount->id,
                        'label' => 'Montura — ' . $mount->name,
                        'type' => 'mount',
                        'payload' => ['mount_id' => $mount->id],
                    ];
                })
                ->values()
                ->all();

            if (!empty($mountOptions)) {
                $optionGroups[] = [
                    'label' => 'Monturas',
                    'options' => $mountOptions,
                ];
                foreach ($mountOptions as $option) {
                    $optionMap[$option['value']] = [
                        'type' => $option['type'],
                        'payload' => $option['payload'],
                    ];
                }
            }
        }

        if (Schema::hasTable('items')) {
            $itemOptions = Item::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $labelType = $item->slot ?: $item->type ?: 'objeto';
                    return [
                        'value' => 'item:' . $item->id,
                        'label' => 'Objeto — ' . $item->name . ' (' . $labelType . ')',
                        'type' => 'item',
                        'payload' => ['item_id' => $item->id, 'quantity' => 1],
                    ];
                })
                ->values()
                ->all();

            if (!empty($itemOptions)) {
                $optionGroups[] = [
                    'label' => 'Objetos',
                    'options' => $itemOptions,
                ];
                foreach ($itemOptions as $option) {
                    $optionMap[$option['value']] = [
                        'type' => $option['type'],
                        'payload' => $option['payload'],
                    ];
                }
            }
        }

        return [$optionMap, $optionGroups];
    }
}
