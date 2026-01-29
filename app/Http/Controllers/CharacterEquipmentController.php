<?php

namespace App\Http\Controllers;

use App\Models\CharacterEquipment;
use App\Models\CharacterItem;
use App\Models\Item;
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

        if (Schema::hasTable('character_items')) {
            $tieneItem = CharacterItem::query()
                ->where('character_id', $character->id)
                ->where('item_id', $item->id)
                ->exists();

            if (!$tieneItem) {
                return back()->withErrors(['item_id' => 'El objeto no está en tu inventario.']);
            }
        }

        CharacterEquipment::updateOrCreate(
            ['character_id' => $character->id, 'slot' => $slot],
            ['item_id' => $item->id]
        );

        if ($slot === 'mount' && Schema::hasColumn('characters', 'has_mount')) {
            $character->has_mount = true;
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

        if ($slot === 'mount' && Schema::hasColumn('characters', 'has_mount')) {
            $character->has_mount = false;
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
}
