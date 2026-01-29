<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Item;
use App\Models\Mount;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CharacterController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            return redirect()->route('game.personaje.edit');
        }

        $adminMount = $this->obtenerMonturaAdmin();

        $races = Race::orderBy('name')->get()->map(function (Race $race) use ($adminMount) {
            $race->sprite = '/assets/sprites/razas/' . Str::slug($race->name) . '.png';

            if ($adminMount && $race->name === 'Aldrik Vhar') {
                $race->base_strength += $adminMount['bonus_strength'];
                $race->base_magic += $adminMount['bonus_magic'];
                $race->base_defense += $adminMount['bonus_defense'];
                $race->base_speed += $adminMount['bonus_speed'];
            }

            return $race;
        });

        $statMax = [
            'fuerza' => max((int) $races->max('base_strength'), 1),
            'magia' => max((int) $races->max('base_magic'), 1),
            'defensa' => max((int) $races->max('base_defense'), 1),
            'velocidad' => max((int) $races->max('base_speed'), 1),
        ];

        return view('game.character.create', [
            'races' => $races,
            'statMax' => $statMax,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            return redirect()->route('game.personaje.edit');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'race_id' => ['nullable', 'exists:races,id'],
        ]);

        $stats = null;
        $mountId = null;
        $hasMount = false;
        if (!empty($validated['race_id'])) {
            $race = Race::find($validated['race_id']);
            if ($race) {
                $stats = [
                    'fuerza' => $race->base_strength,
                    'magia' => $race->base_magic,
                    'defensa' => $race->base_defense,
                    'velocidad' => $race->base_speed,
                ];

                if ($race->name === 'Aldrik Vhar') {
                    $adminMount = $this->obtenerMonturaAdmin();
                    if ($adminMount) {
                        $stats = [
                            'fuerza' => $race->base_strength + $adminMount['bonus_strength'],
                            'magia' => $race->base_magic + $adminMount['bonus_magic'],
                            'defensa' => $race->base_defense + $adminMount['bonus_defense'],
                            'velocidad' => $race->base_speed + $adminMount['bonus_speed'],
                        ];
                        $mountId = $adminMount['id'];
                        $hasMount = true;
                    }
                }
            }
        }

        Character::create([
            'user_id' => $user->id,
            'race_id' => $validated['race_id'] ?? null,
            'name' => $validated['name'],
            'mount_id' => $mountId,
            'stats_json' => $stats,
            'has_mount' => $hasMount,
        ]);

        return redirect()->route('game.personaje.edit');
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->character) {
            return redirect()->route('game.personaje.create');
        }

        $races = Race::orderBy('name')->get();
        $character = $user->character;
        if (Schema::hasTable('mounts')) {
            $character->load(['mount']);
        }
        $items = collect();
        $equipment = collect();
        $inventory = collect();

        if (Schema::hasTable('items')) {
            $items = Item::orderBy('name')->get();
        }

        if (Schema::hasTable('character_equipment')) {
            $equipment = $character->equipment()->with('item')->get()->keyBy('slot');
        }

        if (Schema::hasTable('character_items')) {
            $inventory = $character->inventory()->with('item')->get();
        }

        $slots = [
            'weapon' => 'Arma',
            'helmet' => 'Casco',
            'armor' => 'Armadura',
            'ring' => 'Anillo',
            'amulet' => 'Colgante',
            'mount' => 'Montura',
        ];

        return view('game.character.edit', [
            'character' => $character,
            'races' => $races,
            'items' => $items,
            'equipment' => $equipment,
            'inventory' => $inventory,
            'slots' => $slots,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->character) {
            return redirect()->route('game.personaje.create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'race_id' => ['nullable', 'exists:races,id'],
            'stats.fuerza' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.magia' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.defensa' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.velocidad' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $stats = $this->normalizarStats($validated['stats'] ?? []);

        $user->character->update([
            'race_id' => $validated['race_id'] ?? null,
            'name' => $validated['name'],
            'stats_json' => $stats,
        ]);

        return redirect()->route('game.personaje.edit');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            $user->character->delete();
        }

        return redirect()->route('home');
    }

    private function normalizarStats(array $stats): array
    {
        return [
            'fuerza' => $this->valorEntero($stats['fuerza'] ?? null),
            'magia' => $this->valorEntero($stats['magia'] ?? null),
            'defensa' => $this->valorEntero($stats['defensa'] ?? null),
            'velocidad' => $this->valorEntero($stats['velocidad'] ?? null),
        ];
    }

    private function valorEntero($valor): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return (int) $valor;
    }

    private function obtenerMonturaAdmin(): ?array
    {
        if (!Schema::hasTable('mounts')) {
            return null;
        }

        $mount = Mount::query()->where('is_admin_fixed', true)->first();
        if (!$mount) {
            return null;
        }

        return [
            'id' => $mount->id,
            'bonus_strength' => (int) $mount->bonus_strength,
            'bonus_magic' => (int) $mount->bonus_magic,
            'bonus_defense' => (int) $mount->bonus_defense,
            'bonus_speed' => (int) $mount->bonus_speed,
        ];
    }
}
