<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Item;
use App\Models\CharacterItem;
use App\Models\CharacterPotionEffect;
use Illuminate\Support\Facades\DB;

class PotionService
{
    public function useHealingPotion(Character $character, Item $item): bool
    {
        // Verificar si ya hay heal_lock activo
        $activeHealLock = CharacterPotionEffect::where('character_id', $character->id)
            ->where('effect_type', 'heal_lock')
            ->where('remaining_missions', '>', 0)
            ->first();

        if ($activeHealLock) {
            return false; // Ya usado en esta misión
        }

        // Curar HP
        $character->hp_current = $character->hp_max;
        $character->save();

        // Crear heal_lock
        CharacterPotionEffect::create([
            'character_id' => $character->id,
            'effect_type' => 'heal_lock',
            'remaining_missions' => 1,
        ]);

        // Consumir item del inventario
        $this->consumeItem($character, $item);

        return true;
    }

    public function useStatPotion(Character $character, Item $item, string $stat, int $bonus = 1): bool
    {
        // Verificar si ya hay stat_boost activo
        $activeStatBoost = CharacterPotionEffect::where('character_id', $character->id)
            ->where('effect_type', 'stat_boost')
            ->where('remaining_missions', '>', 0)
            ->first();

        if ($activeStatBoost) {
            return false; // Ya tienes un buff activo
        }

        // Crear stat_boost
        CharacterPotionEffect::create([
            'character_id' => $character->id,
            'effect_type' => 'stat_boost',
            'stat' => $stat,
            'bonus' => $bonus,
            'remaining_missions' => 1,
        ]);

        // Consumir item
        $this->consumeItem($character, $item);

        return true;
    }

    private function consumeItem(Character $character, Item $item): void
    {
        $inventoryItem = CharacterItem::where('character_id', $character->id)
            ->where('item_id', $item->id)
            ->first();

        if ($inventoryItem) {
            if ($inventoryItem->quantity > 1) {
                $inventoryItem->decrement('quantity');
            } else {
                $inventoryItem->delete();
            }
        }
    }
}
