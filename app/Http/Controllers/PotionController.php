<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Item;
use App\Services\PotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PotionController extends Controller
{
    protected PotionService $potionService;

    public function __construct(PotionService $potionService)
    {
        $this->potionService = $potionService;
    }

    public function usePotion(Request $request, Item $item)
    {
        $user = Auth::user();
        $character = $user->character;

        if (!$character) {
            return back()->with('error', 'Necesitas personaje para usar pociones');
        }

        if (!$this->characterOwnsPotion($character, $item)) {
            return back()->with('error', 'No tienes esa poción disponible');
        }

        $validated = $request->validate([
            'type' => 'nullable|in:heal,stat',
            'stat' => 'nullable|in:strength,magic,defense,speed',
        ]);

        [$type, $stat] = $this->resolvePotionType($item, $validated['type'] ?? null, $validated['stat'] ?? null);
        if (!$type) {
            return back()->with('error', 'No se pudo determinar el tipo de poción');
        }

        if ($type === 'heal') {
            $success = $this->potionService->useHealingPotion($character, $item);
            if (!$success) {
                return back()->with('error', 'Ya has usado curación en esta misión');
            }
            return back()->with('success', 'Poción de curación usada');
        } elseif ($type === 'stat') {
            if (!$stat) {
                return back()->with('error', 'Debes seleccionar un stat para la poción');
            }
            $success = $this->potionService->useStatPotion($character, $item, $stat);
            if (!$success) {
                $active = \App\Models\CharacterPotionEffect::where('character_id', $character->id)
                    ->where('effect_type', 'stat_boost')
                    ->where('remaining_missions', '>', 0)
                    ->first();
                $statName = $active ? $active->stat : 'desconocido';
                return back()->with('error', 'Ya tienes una poción de stat activa: ' . $statName);
            }
            return back()->with('success', 'Poción de stat usada');
        }

        return back()->with('error', 'Tipo de poción inválido');
    }

    public function usePotionFromSelection(Request $request)
    {
        // Entrada desde equipamiento: selecciona poción por id.
        $user = Auth::user();
        $character = $user->character;

        if (!$character) {
            return back()->with('error', 'Necesitas personaje para usar pociones');
        }

        $validated = $request->validate([
            'item_id' => 'required|integer',
            'type' => 'nullable|in:heal,stat',
            'stat' => 'nullable|in:strength,magic,defense,speed',
        ]);

        $item = Item::find($validated['item_id']);
        if (!$item) {
            return back()->with('error', 'Poción inválida');
        }

        if (!$this->characterOwnsPotion($character, $item)) {
            return back()->with('error', 'No tienes esa poción disponible');
        }

        [$type, $stat] = $this->resolvePotionType($item, $validated['type'] ?? null, $validated['stat'] ?? null);
        if (!$type) {
            return back()->with('error', 'No se pudo determinar el tipo de poción');
        }

        if ($type === 'heal') {
            $success = $this->potionService->useHealingPotion($character, $item);
            if (!$success) {
                return back()->with('error', 'Ya has usado curación en esta misión');
            }
            return back()->with('success', 'Poción de curación usada');
        }

        if ($type === 'stat') {
            if (!$stat) {
                return back()->with('error', 'Debes seleccionar un stat para la poción');
            }
            $success = $this->potionService->useStatPotion($character, $item, $stat);
            if (!$success) {
                $active = \App\Models\CharacterPotionEffect::where('character_id', $character->id)
                    ->where('effect_type', 'stat_boost')
                    ->where('remaining_missions', '>', 0)
                    ->first();
                $statName = $active ? $active->stat : 'desconocido';
                return back()->with('error', 'Ya tienes una poción de stat activa: ' . $statName);
            }
            return back()->with('success', 'Poción de stat usada');
        }

        return back()->with('error', 'Tipo de poción inválido');
    }

    private function characterOwnsPotion($character, Item $item): bool
    {
        if (!$character || $item->type !== 'potion') {
            return false;
        }

        return $character->inventory()
            ->where('item_id', $item->id)
            ->where('quantity', '>', 0)
            ->exists();
    }

    private function resolvePotionType(Item $item, ?string $type, ?string $stat): array
    {
        if ($type) {
            return [$type, $stat];
        }

        $name = strtolower($item->name ?? '');
        if (str_contains($name, 'curación') || str_contains($name, 'cura')) {
            return ['heal', null];
        }

        $statMap = [
            'fuerza' => 'strength',
            'magia' => 'magic',
            'defensa' => 'defense',
            'velocidad' => 'speed',
        ];

        foreach ($statMap as $key => $value) {
            if (str_contains($name, $key)) {
                return ['stat', $value];
            }
        }

        return [null, null];
    }
}
