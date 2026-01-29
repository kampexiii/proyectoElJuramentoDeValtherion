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

        $validated = $request->validate([
            'type' => 'required|in:heal,stat',
            'stat' => 'nullable|in:strength,magic,defense,speed',
        ]);

        if ($validated['type'] === 'heal') {
            $success = $this->potionService->useHealingPotion($character, $item);
            if (!$success) {
                return back()->with('error', 'Ya has usado curación en esta misión');
            }
            return back()->with('success', 'Poción de curación usada');
        } elseif ($validated['type'] === 'stat') {
            if (!$validated['stat']) {
                return back()->with('error', 'Debes seleccionar un stat para la poción');
            }
            $success = $this->potionService->useStatPotion($character, $item, $validated['stat']);
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
}
