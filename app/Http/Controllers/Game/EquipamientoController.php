<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\CharacterEquipment;
use App\Models\CharacterItem;
use App\Models\Item;
use App\Models\Mount;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EquipamientoController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $character = $user?->character;

        if (!$character) {
            return redirect()
                ->route('game.personaje.create')
                ->with('status', 'Necesitas un personaje para equiparte.');
        }

        if (!Schema::hasTable('items') || !Schema::hasTable('character_items')) {
            return view('game.equipamiento', [
                'options' => $this->emptyOptions(),
                'current' => [],
                'showMount' => false,
            ]);
        }

        $inventoryRows = CharacterItem::query()
            ->with('item')
            ->where('character_id', $character->id)
            ->get();

        $inventoryItems = $inventoryRows->pluck('item')->filter();

        $mountOptions = $this->optionsForSlot($inventoryItems, 'mount');
        // Si el personaje tiene montura real, añadirla como opción extra (si no está ya)
        if ($character && $character->mount_id && Schema::hasTable('mounts')) {
            $realMount = Mount::find($character->mount_id);
            if ($realMount && !$mountOptions->contains('id', $realMount->id)) {
                $mountOptions = $mountOptions->push($realMount);
            }
        }
        $options = [
            'helmet' => $this->optionsForSlot($inventoryItems, 'helmet'),
            'armor' => $this->optionsForSlot($inventoryItems, 'armor'),
            'weapon' => $this->optionsForSlot($inventoryItems, 'weapon'),
            'ring' => $this->optionsForSlot($inventoryItems, 'ring'),
            'amulet' => $this->optionsForSlot($inventoryItems, 'amulet'),
            'mount' => $mountOptions,
        ];

        $equipment = collect();
        if (Schema::hasTable('character_equipment')) {
            $equipment = CharacterEquipment::query()
                ->where('character_id', $character->id)
                ->get()
                ->keyBy('slot');
        }

        $current = [];
        foreach ($options as $slot => $items) {
            $current[$slot] = $equipment->get($slot)?->item_id;
        }

        return view('game.equipamiento', [
            'options' => $options,
            'current' => $current,
            'showMount' => true,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $character = $user?->character;

        if (!$character) {
            return redirect()
                ->route('game.personaje.create')
                ->with('status', 'Necesitas un personaje para equiparte.');
        }

        if (!Schema::hasTable('items') || !Schema::hasTable('character_items') || !Schema::hasTable('character_equipment')) {
            return back()->withErrors(['equipamiento' => 'Aún no hay sistema de equipamiento activo.']);
        }

        $validated = $request->validate([
            'helmet_item_id' => ['nullable', 'integer'],
            'armor_item_id' => ['nullable', 'integer'],
            'weapon_item_id' => ['nullable', 'integer'],
            'ring_item_id' => ['nullable', 'integer'],
            'amulet_item_id' => ['nullable', 'integer'],
            'mount_item_id' => ['nullable', 'integer'],
        ]);

        if ($character->mount_id && !empty($validated['mount_item_id'])) {
            return back()->withErrors(['mount_item_id' => 'La montura fija no se puede cambiar.'])->withInput();
        }


        $inventoryRows = CharacterItem::query()
            ->with('item')
            ->where('character_id', $character->id)
            ->get();

        $inventoryItems = $inventoryRows->pluck('item')->filter()->keyBy('id');

        // Si el personaje tiene montura real, añadirla como opción extra (si no está ya)
        if ($character && $character->mount_id && Schema::hasTable('mounts')) {
            $realMount = Mount::find($character->mount_id);
            if ($realMount && !$inventoryItems->has($realMount->id)) {
                $inventoryItems->put($realMount->id, $realMount);
            }
        }

        $selected = [];
        $slotMap = [
            'helmet' => 'helmet_item_id',
            'armor' => 'armor_item_id',
            'weapon' => 'weapon_item_id',
            'ring' => 'ring_item_id',
            'amulet' => 'amulet_item_id',
            'mount' => 'mount_item_id',
        ];

        foreach ($slotMap as $slot => $field) {
            $itemId = $validated[$field] ?? null;
            if (!$itemId) {
                $selected[$slot] = null;
                continue;
            }

            $item = $inventoryItems->get((int) $itemId);
            if (!$item) {
                // Permitir equipar la montura real aunque no esté en inventario
                if ($slot === 'mount' && $character->mount_id && (int)$itemId === (int)$character->mount_id) {
                    $item = Mount::find($character->mount_id);
                } else {
                    return back()->withErrors([$field => 'No posees ese objeto.'])->withInput();
                }
            }

            if (!$this->itemMatchesSlot($item, $slot)) {
                return back()->withErrors([$field => 'Ese objeto no corresponde a este hueco.'])->withInput();
            }

            $selected[$slot] = $item;
        }

        $equipment = CharacterEquipment::query()
            ->where('character_id', $character->id)
            ->with('item')
            ->get()
            ->keyBy('slot');

        foreach ($selected as $slot => $item) {
            if ($slot === 'mount' && $character->mount_id) {
                continue;
            }
            $equipment[$slot] = (object) ['item' => $item];
        }

        if ($this->superaMaximos($character, $equipment)) {
            return back()->withErrors(['equipamiento' => 'Ese equipamiento supera el máximo permitido.'])->withInput();
        }

        foreach ($selected as $slot => $item) {
            if ($slot === 'mount' && $character->mount_id) {
                continue;
            }
            CharacterEquipment::updateOrCreate(
                ['character_id' => $character->id, 'slot' => $slot],
                ['item_id' => $item?->id]
            );
        }

        if (Schema::hasColumn('characters', 'has_mount')) {
            $tieneMontura = $character->mount_id !== null;
            if (!$tieneMontura && !empty($selected['mount'])) {
                $tieneMontura = true;
            }
            if (!$tieneMontura) {
                $tieneMontura = CharacterEquipment::query()
                    ->where('character_id', $character->id)
                    ->where('slot', 'mount')
                    ->whereNotNull('item_id')
                    ->exists();
            }
            $character->has_mount = $tieneMontura;
            $character->save();
        }

        return back()->with('status', 'Equipamiento actualizado.');
    }

    private function emptyOptions(): array
    {
        return [
            'helmet' => collect(),
            'armor' => collect(),
            'weapon' => collect(),
            'ring' => collect(),
            'amulet' => collect(),
            'mount' => collect(),
        ];
    }

    private function optionsForSlot(Collection $items, string $slot): Collection
    {
        return $items->filter(function ($item) use ($slot) {
            return $this->itemMatchesSlot($item, $slot);
        })->values();
    }

    private function itemMatchesSlot(Item $item, string $slot): bool
    {
        $normalizedSlot = $this->normalizarSlot($slot);
        $itemSlot = $this->normalizarSlot($item->slot ?: $item->type);

        if ($itemSlot === '') {
            return false;
        }

        $map = [
            'head' => 'helmet',
            'helmet' => 'helmet',
            'chest' => 'armor',
            'armor' => 'armor',
        ];

        $itemSlot = $map[$itemSlot] ?? $itemSlot;
        $normalizedSlot = $map[$normalizedSlot] ?? $normalizedSlot;

        return $itemSlot === $normalizedSlot;
    }

    private function normalizarSlot(?string $slot): string
    {
        if (!$slot) {
            return '';
        }

        return Str::of($slot)->lower()->trim()->value();
    }

    private function superaMaximos(Character $character, $equipment): bool
    {
        $race = $character->race;
        $base = $this->statsBase($race);
        $bonusEquipo = $this->bonusesFromEquipment($equipment);
        $bonusMontura = $this->bonusMonturaFija($character);
        $maximos = $this->statsMaximos($race, $bonusMontura);

        foreach ($base as $key => $valor) {
            $actual = $valor + ($bonusEquipo[$key] ?? 0) + ($bonusMontura[$key] ?? 0);
            if ($actual > ($maximos[$key] ?? $actual)) {
                return true;
            }
        }

        return false;
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
}
