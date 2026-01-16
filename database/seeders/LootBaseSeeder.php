<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class LootBaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Date::now();

        $rarityCommonId = DB::table('rarities')->where('name', 'common')->value('id');
        $rarityRareId = DB::table('rarities')->where('name', 'rare')->value('id');
        $rarityEpicId = DB::table('rarities')->where('name', 'epic')->value('id');

        if (!$rarityCommonId || !$rarityRareId || !$rarityEpicId) {
            return;
        }

        DB::table('items')->upsert([
            [
                'name' => 'Espada de Hierro',
                'type' => 'weapon',
                'rarity_id' => $rarityCommonId,
                'required_level' => 1,
                'stackable' => false,
                'bonus_hp' => 0,
                'bonus_strength' => 2,
                'bonus_magic' => 0,
                'bonus_defense' => 0,
                'bonus_speed' => 0,
                'sell_price' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Armadura Reforzada',
                'type' => 'armor',
                'rarity_id' => $rarityRareId,
                'required_level' => 5,
                'stackable' => false,
                'bonus_hp' => 5,
                'bonus_strength' => 0,
                'bonus_magic' => 0,
                'bonus_defense' => 3,
                'bonus_speed' => -1,
                'sell_price' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Anillo Arcano',
                'type' => 'accessory',
                'rarity_id' => $rarityEpicId,
                'required_level' => 20,
                'stackable' => false,
                'bonus_hp' => 0,
                'bonus_strength' => 0,
                'bonus_magic' => 6,
                'bonus_defense' => 1,
                'bonus_speed' => 1,
                'sell_price' => 200,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['name'], ['type', 'rarity_id', 'required_level', 'stackable', 'bonus_hp', 'bonus_strength', 'bonus_magic', 'bonus_defense', 'bonus_speed', 'sell_price', 'updated_at']);

        DB::table('loot_tables')->upsert([
            [
                'name' => 'cofre_ganador_racial',
                'rolls' => 3,
                'min_level' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['name'], ['rolls', 'min_level', 'updated_at']);

        $lootTableId = DB::table('loot_tables')->where('name', 'cofre_ganador_racial')->value('id');
        if (!$lootTableId) {
            return;
        }

        DB::table('chests')->upsert([
            [
                'name' => 'Cofre de Campeón Racial',
                'loot_table_id' => $lootTableId,
                'items_count' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['name'], ['loot_table_id', 'items_count', 'updated_at']);

        $itemCommonId = DB::table('items')->where('name', 'Espada de Hierro')->value('id');
        $itemRareId = DB::table('items')->where('name', 'Armadura Reforzada')->value('id');
        $itemEpicId = DB::table('items')->where('name', 'Anillo Arcano')->value('id');

        if (!$itemCommonId || !$itemRareId || !$itemEpicId) {
            return;
        }

        $existing = DB::table('loot_entries')
            ->where('loot_table_id', $lootTableId)
            ->count();

        if ($existing === 0) {
            DB::table('loot_entries')->insert([
                [
                    'loot_table_id' => $lootTableId,
                    'entry_type' => 'item',
                    'item_id' => $itemCommonId,
                    'animal_id' => null,
                    'weight' => 700,
                    'min_qty' => 1,
                    'max_qty' => 1,
                    'required_level' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'loot_table_id' => $lootTableId,
                    'entry_type' => 'item',
                    'item_id' => $itemRareId,
                    'animal_id' => null,
                    'weight' => 250,
                    'min_qty' => 1,
                    'max_qty' => 1,
                    'required_level' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'loot_table_id' => $lootTableId,
                    'entry_type' => 'item',
                    'item_id' => $itemEpicId,
                    'animal_id' => null,
                    'weight' => 45,
                    'min_qty' => 1,
                    'max_qty' => 1,
                    'required_level' => 20,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }
}
