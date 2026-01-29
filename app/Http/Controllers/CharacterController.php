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

            if ($adminMount && $race->name === 'Señor Legendario del Caos') {
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

                if ($race->name === 'Señor Legendario del Caos') {
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

        $character = $user->character->load(['race']);
        if (Schema::hasTable('mounts')) {
            $character->load(['mount']);
        }
        $this->asegurarMonturaAdmin($character, $user);
        if (Schema::hasTable('mounts')) {
            $character->load(['mount']);
        }
        $items = collect();
        $equipment = collect();
        $inventory = collect();

        if (Schema::hasTable('character_equipment')) {
            $equipment = $character->equipment()->with('item')->get()->keyBy('slot');
        }

        if (Schema::hasTable('character_items')) {
            $inventory = $character->inventory()->with('item')->get();
            $items = $inventory->pluck('item')->filter()->unique('id')->values();
        }

        $statsView = $this->statsVista($character, $equipment);

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
            'items' => $items,
            'equipment' => $equipment,
            'inventory' => $inventory,
            'slots' => $slots,
            'statsView' => $statsView,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->character) {
            return redirect()->route('game.personaje.create');
        }

        return back()->withErrors(['name' => 'El nombre, la raza y los stats no se pueden editar.']);
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

    private function statsVista(Character $character, $equipment): array
    {
        $race = $character->race;
        $base = $this->statsBase($race);
        $bonusEquipo = $this->bonusesFromEquipment($equipment);
        $bonusMontura = $this->bonusMonturaFija($character);
        $maximos = $this->statsMaximos($race, $bonusMontura);

        $actual = [];
        foreach ($base as $key => $valor) {
            $suma = $valor + ($bonusEquipo[$key] ?? 0) + ($bonusMontura[$key] ?? 0);
            $actual[$key] = min($suma, $maximos[$key] ?? $suma);
        }

        return $this->formatoBarras($actual, $maximos);
    }

    private function formatoBarras(array $actual, array $maximos): array
    {
        $resultado = [];
        foreach ($actual as $key => $valor) {
            $max = max((int) ($maximos[$key] ?? 1), 1);
            $ratio = $valor / $max;
            $step = (int) (round($ratio * 10) * 10);
            $step = max(0, min(100, $step));

            $resultado[$key] = [
                'valor' => $valor,
                'max' => $max,
                'clase' => 'hx-bar-' . $step,
                'color' => $this->colorPorcentaje($ratio),
            ];
        }

        return $resultado;
    }

    private function colorPorcentaje(float $ratio): string
    {
        if ($ratio >= 0.75) {
            return 'bg-success';
        }

        if ($ratio >= 0.5) {
            return 'bg-info';
        }

        if ($ratio >= 0.25) {
            return 'bg-warning';
        }

        return 'bg-danger';
    }

    private function statsBase(?Race $race): array
    {
        return [
            'fuerza' => (int) ($race->base_strength ?? 0),
            'magia' => (int) ($race->base_magic ?? 0),
            'defensa' => (int) ($race->base_defense ?? 0),
            'velocidad' => (int) ($race->base_speed ?? 0),
        ];
    }

    private function statsMaximos(?Race $race, array $bonusMontura): array
    {
        $caps = $race?->caps_json ?? [];
        $max = [
            'fuerza' => $this->capDe($caps, 'fuerza', 'strength'),
            'magia' => $this->capDe($caps, 'magia', 'magic'),
            'defensa' => $this->capDe($caps, 'defensa', 'defense'),
            'velocidad' => $this->capDe($caps, 'velocidad', 'speed'),
        ];

        $tieneCaps = collect($max)->filter()->count() > 0;
        if (!$tieneCaps && Schema::hasTable('races')) {
            $max = [
                'fuerza' => (int) Race::max('base_strength'),
                'magia' => (int) Race::max('base_magic'),
                'defensa' => (int) Race::max('base_defense'),
                'velocidad' => (int) Race::max('base_speed'),
            ];
        }

        if ($race && in_array($race->name, ['Señor Legendario del Caos', 'Aldrik Vhar'], true)) {
            $max = [
                'fuerza' => max($max['fuerza'] ?? 0, (int) $race->base_strength + ($bonusMontura['fuerza'] ?? 0)),
                'magia' => max($max['magia'] ?? 0, (int) $race->base_magic + ($bonusMontura['magia'] ?? 0)),
                'defensa' => max($max['defensa'] ?? 0, (int) $race->base_defense + ($bonusMontura['defensa'] ?? 0)),
                'velocidad' => max($max['velocidad'] ?? 0, (int) $race->base_speed + ($bonusMontura['velocidad'] ?? 0)),
            ];
        }

        return [
            'fuerza' => max(1, (int) ($max['fuerza'] ?? 1)),
            'magia' => max(1, (int) ($max['magia'] ?? 1)),
            'defensa' => max(1, (int) ($max['defensa'] ?? 1)),
            'velocidad' => max(1, (int) ($max['velocidad'] ?? 1)),
        ];
    }

    private function capDe(array $caps, string $es, string $en): ?int
    {
        if (array_key_exists($es, $caps)) {
            return (int) $caps[$es];
        }

        if (array_key_exists($en, $caps)) {
            return (int) $caps[$en];
        }

        return null;
    }

    private function bonusesFromEquipment($equipment): array
    {
        $bonus = [
            'fuerza' => 0,
            'magia' => 0,
            'defensa' => 0,
            'velocidad' => 0,
        ];

        foreach ($equipment as $entry) {
            $item = $entry->item ?? null;
            if (!$item) {
                continue;
            }

            $itemBonus = $this->bonusesDeItem($item);
            foreach ($bonus as $key => $valor) {
                $bonus[$key] += (int) ($itemBonus[$key] ?? 0);
            }
        }

        return $bonus;
    }

    private function bonusesDeItem(Item $item): array
    {
        $bonuses = is_array($item->bonuses_json ?? null) ? $item->bonuses_json : [];

        return [
            'fuerza' => (int) ($bonuses['fuerza'] ?? $bonuses['strength'] ?? $item->bonus_strength ?? 0),
            'magia' => (int) ($bonuses['magia'] ?? $bonuses['magic'] ?? $item->bonus_magic ?? 0),
            'defensa' => (int) ($bonuses['defensa'] ?? $bonuses['defense'] ?? $item->bonus_defense ?? 0),
            'velocidad' => (int) ($bonuses['velocidad'] ?? $bonuses['speed'] ?? $item->bonus_speed ?? 0),
        ];
    }

    private function bonusMonturaFija(Character $character): array
    {
        if (!$character->mount_id || !Schema::hasTable('mounts')) {
            return [
                'fuerza' => 0,
                'magia' => 0,
                'defensa' => 0,
                'velocidad' => 0,
            ];
        }

        $mount = Mount::find($character->mount_id);
        if (!$mount) {
            return [
                'fuerza' => 0,
                'magia' => 0,
                'defensa' => 0,
                'velocidad' => 0,
            ];
        }

        return [
            'fuerza' => (int) $mount->bonus_strength,
            'magia' => (int) $mount->bonus_magic,
            'defensa' => (int) $mount->bonus_defense,
            'velocidad' => (int) $mount->bonus_speed,
        ];
    }

    private function asegurarMonturaAdmin(Character $character, $user): void
    {
        if (!$user || ($user->role ?? null) !== 'admin') {
            return;
        }

        $race = $character->race;
        if (!$race) {
            return;
        }

        if (!in_array($race->name, ['Señor Legendario del Caos', 'Aldrik Vhar'], true)) {
            return;
        }

        if (!Schema::hasTable('mounts')) {
            return;
        }

        $adminMount = Mount::query()->where('is_admin_fixed', true)->first();
        if (!$adminMount) {
            return;
        }

        $base = $this->statsBase($race);
        $bonus = [
            'fuerza' => (int) $adminMount->bonus_strength,
            'magia' => (int) $adminMount->bonus_magic,
            'defensa' => (int) $adminMount->bonus_defense,
            'velocidad' => (int) $adminMount->bonus_speed,
        ];
        $maximos = $this->statsMaximos($race, $bonus);
        $stats = [];
        foreach ($base as $key => $valor) {
            $stats[$key] = min($valor + ($bonus[$key] ?? 0), $maximos[$key] ?? ($valor + ($bonus[$key] ?? 0)));
        }

        $actualizar = false;
        if ($character->mount_id !== $adminMount->id) {
            $character->mount_id = $adminMount->id;
            $actualizar = true;
        }

        if (Schema::hasColumn('characters', 'has_mount') && !$character->has_mount) {
            $character->has_mount = true;
            $actualizar = true;
        }

        if ($character->stats_json !== $stats) {
            $character->stats_json = $stats;
            $actualizar = true;
        }

        if ($actualizar) {
            $character->save();
        }
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
