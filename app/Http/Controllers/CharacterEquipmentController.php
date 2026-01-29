<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterEquipment;
use App\Models\CharacterItem;
use App\Models\Item;
use App\Models\Mount;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CharacterEquipmentController extends Controller
{
    public function equip(Request $request)
    {
        $character = $request->user()?->character;
        if (!$character) {
            return redirect()->route('game.personaje.create');
        }

        if (!Schema::hasTable('items') || !Schema::hasTable('character_equipment')) {
            return back()->withErrors(['item_id' => 'Aún no hay sistema de equipamiento activo.']);
        }

        $validated = $request->validate([
            'slot' => ['required', 'string'],
            'item_id' => ['required', 'integer'],
        ]);

        $slot = $this->normalizarSlot($validated['slot']);
        if (!$this->slotValido($slot)) {
            return back()->withErrors(['slot' => 'Slot no válido.']);
        }

        if (!Schema::hasTable('items')) {
            return back()->withErrors(['item_id' => 'Aún no hay objetos disponibles.']);
        }

        $item = Item::find($validated['item_id']);
        if (!$item) {
            return back()->withErrors(['item_id' => 'El objeto no existe.']);
        }

        if ($character->mount_id && $slot === 'mount') {
            return back()->withErrors(['slot' => 'La montura fija no se puede cambiar.']);
        }

        $itemSlot = $this->slotDeItem($item);
        if (!$itemSlot || $itemSlot !== $slot) {
            return back()->withErrors(['item_id' => 'El objeto no corresponde al slot seleccionado.']);
        }

        if (!Schema::hasTable('character_items')) {
            return back()->withErrors(['item_id' => 'Aún no hay inventario activo.']);
        }

        $tieneItem = CharacterItem::query()
            ->where('character_id', $character->id)
            ->where('item_id', $item->id)
            ->exists();

        if (!$tieneItem) {
            return back()->withErrors(['item_id' => 'El objeto no está en tu inventario.']);
        }

        $equipment = CharacterEquipment::query()
            ->where('character_id', $character->id)
            ->with('item')
            ->get()
            ->keyBy('slot');

        $equipment[$slot] = (object) ['item' => $item];

        if ($this->superaMaximos($character, $equipment)) {
            return back()->withErrors(['item_id' => 'Ese equipo supera el máximo permitido para tus stats.']);
        }

        CharacterEquipment::updateOrCreate(
            ['character_id' => $character->id, 'slot' => $slot],
            ['item_id' => $item->id]
        );

        if (Schema::hasColumn('characters', 'has_mount')) {
            $tieneMontura = $character->mount_id !== null;
            if ($slot === 'mount') {
                $tieneMontura = true;
            } elseif (Schema::hasTable('character_equipment')) {
                $tieneMontura = $tieneMontura || CharacterEquipment::query()
                    ->where('character_id', $character->id)
                    ->where('slot', 'mount')
                    ->whereNotNull('item_id')
                    ->exists();
            }
            $character->has_mount = $tieneMontura;
            $character->save();
        }

        return back()->with('status', 'Equipo actualizado.');
    }

    public function unequip(Request $request)
    {
        $character = $request->user()?->character;
        if (!$character) {
            return redirect()->route('game.personaje.create');
        }

        if (!Schema::hasTable('character_equipment')) {
            return back()->withErrors(['slot' => 'Aún no hay sistema de equipamiento activo.']);
        }

        $validated = $request->validate([
            'slot' => ['required', 'string'],
        ]);

        $slot = $this->normalizarSlot($validated['slot']);
        if (!$this->slotValido($slot)) {
            return back()->withErrors(['slot' => 'Slot no válido.']);
        }

        if ($character->mount_id && $slot === 'mount') {
            return back()->withErrors(['slot' => 'La montura fija no se puede quitar.']);
        }

        CharacterEquipment::updateOrCreate(
            ['character_id' => $character->id, 'slot' => $slot],
            ['item_id' => null]
        );

        if (Schema::hasColumn('characters', 'has_mount')) {
            $tieneMontura = $character->mount_id !== null;
            if (Schema::hasTable('character_equipment')) {
                $tieneMontura = $tieneMontura || CharacterEquipment::query()
                    ->where('character_id', $character->id)
                    ->where('slot', 'mount')
                    ->whereNotNull('item_id')
                    ->exists();
            }
            $character->has_mount = $tieneMontura;
            $character->save();
        }

        return back()->with('status', 'Equipo actualizado.');
    }

    private function slotValido(string $slot): bool
    {
        return in_array($slot, $this->slotsPermitidos(), true);
    }

    private function slotsPermitidos(): array
    {
        return ['weapon', 'helmet', 'armor', 'ring', 'amulet', 'mount'];
    }

    private function normalizarSlot(string $slot): string
    {
        return Str::of($slot)->lower()->trim()->value();
    }

    private function slotDeItem(Item $item): ?string
    {
        $slot = $item->slot ?: $item->type;
        if (!$slot) {
            return null;
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

        if ($race && $race->name === 'Aldrik Vhar') {
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
