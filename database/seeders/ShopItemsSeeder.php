<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        $hasCode = Schema::hasColumn('items', 'code');
        $hasSlot = Schema::hasColumn('items', 'slot');
        $hasValueGold = Schema::hasColumn('items', 'value_gold');
        $hasSellPrice = Schema::hasColumn('items', 'sell_price');
        $hasBonusesJson = Schema::hasColumn('items', 'bonuses_json');
        $hasEffectsJson = Schema::hasColumn('items', 'effects_json');
        $hasBonusStrength = Schema::hasColumn('items', 'bonus_strength');
        $hasBonusMagic = Schema::hasColumn('items', 'bonus_magic');
        $hasBonusDefense = Schema::hasColumn('items', 'bonus_defense');
        $hasBonusSpeed = Schema::hasColumn('items', 'bonus_speed');
        $hasBonusHp = Schema::hasColumn('items', 'bonus_hp');
        $hasStackable = Schema::hasColumn('items', 'stackable');
        $hasMaxStack = Schema::hasColumn('items', 'max_stack');
        $hasIsConsumable = Schema::hasColumn('items', 'is_consumable');
        $hasRarityId = Schema::hasColumn('items', 'rarity_id');
        $hasRarity = Schema::hasColumn('items', 'rarity');

        $rarityMap = [];
        if ($hasRarityId) {
            if (!Schema::hasTable('rarities')) {
                return;
            }

            $rarities = [
                'common' => 'Común',
                'rare' => 'Rara',
                'epic' => 'Épica',
            ];

            foreach ($rarities as $code => $name) {
                DB::table('rarities')->updateOrInsert(
                    ['code' => $code],
                    ['name' => $name, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            $rarityMap = DB::table('rarities')
                ->whereIn('code', array_keys($rarities))
                ->pluck('id', 'code')
                ->toArray();
        }

        $items = [
            [
                'code' => 'armor_head_vigia_paramo',
                'name' => 'Casco del Vigía del Páramo',
                'type' => 'armor',
                'slot' => 'helmet',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 60,
                'bonuses' => ['defense' => 1],
                'description' => 'Hierro curtido y juramentos viejos. Protege lo justo para sobrevivir.',
            ],
            [
                'code' => 'armor_chest_juramento_gris',
                'name' => 'Coraza del Juramento Gris',
                'type' => 'armor',
                'slot' => 'armor',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 140,
                'bonuses' => ['defense' => 2],
                'description' => 'Forjada para aguantar embates en la Sala de Guerra.',
            ],
            [
                'code' => 'armor_chest_bastion_negro',
                'name' => 'Placas del Bastión Negro',
                'type' => 'armor',
                'slot' => 'armor',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 280,
                'bonuses' => ['defense' => 3],
                'description' => 'Pesa como una condena, pero detiene la muerte un instante más.',
            ],
            [
                'code' => 'weapon_sword_filo_ceniza',
                'name' => 'Espada — Filo de Ceniza',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 70,
                'bonuses' => ['strength' => 1],
                'description' => 'Acero simple. Mata igual si el brazo no tiembla.',
            ],
            [
                'code' => 'weapon_axe_rompejuramentos',
                'name' => 'Hacha — Rompejuramentos',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 160,
                'bonuses' => ['strength' => 2],
                'description' => 'Cada golpe suena como una promesa rota.',
            ],
            [
                'code' => 'weapon_mace_martillo_alba',
                'name' => 'Maza — Martillo del Alba',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 300,
                'bonuses' => ['strength' => 3],
                'description' => 'No corta. Aplasta. Y eso basta.',
            ],
            [
                'code' => 'weapon_bow_susurro_cuervo',
                'name' => 'Arco — Susurro del Cuervo',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 75,
                'bonuses' => ['speed' => 1],
                'description' => 'Ligero, rápido. Golpea antes de que el enemigo piense.',
            ],
            [
                'code' => 'weapon_dagger_colmillo_sombrio',
                'name' => 'Daga — Colmillo Sombrío',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 150,
                'bonuses' => ['speed' => 2],
                'description' => 'Para los que prefieren un final silencioso.',
            ],
            [
                'code' => 'weapon_staff_vara_grieta',
                'name' => 'Bastón — Vara de la Grieta',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 320,
                'bonuses' => ['magic' => 3],
                'description' => 'La madera está viva. Y no le gusta la luz.',
            ],
            [
                'code' => 'acc_ring_sello_viejo',
                'name' => 'Anillo — Sello Viejo',
                'type' => 'accessory',
                'slot' => 'ring',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 55,
                'bonuses' => ['magic' => 1],
                'description' => 'Una runa gastada. Aun así… responde.',
            ],
            [
                'code' => 'acc_ring_guardia_roca',
                'name' => 'Anillo — Guardia de Roca',
                'type' => 'accessory',
                'slot' => 'ring',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 120,
                'bonuses' => ['defense' => 2],
                'description' => 'Promete firmeza. No promete victoria.',
            ],
            [
                'code' => 'acc_amulet_vela_negra',
                'name' => 'Talismán — La Vela Negra',
                'type' => 'accessory',
                'slot' => 'amulet',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 260,
                'bonuses' => ['hp' => 3],
                'description' => 'Dicen que alarga la vida… a cambio de algo que no se ve.',
            ],
            [
                'code' => 'mount_horse_corcel_guerra',
                'name' => 'Montura — Corcel de Guerra',
                'type' => 'mount',
                'slot' => 'mount',
                'rarity' => 'rare',
                'required_level' => 8,
                'price' => 350,
                'bonuses' => ['speed' => 2, 'defense' => 1],
                'description' => 'No es un caballo manso. Es un arma con patas.',
            ],
        ];

        foreach ($items as $item) {
            $key = $hasCode ? ['code' => $item['code']] : ['name' => $item['name']];
            $data = [
                'name' => $item['name'],
                'type' => $item['type'],
                'required_level' => $item['required_level'],
            ];

            if ($hasCode) {
                $data['code'] = $item['code'];
            }

            if ($hasSlot) {
                $data['slot'] = $item['slot'];
            }

            if ($hasValueGold) {
                $data['value_gold'] = $item['price'];
            } elseif ($hasSellPrice) {
                $data['sell_price'] = $item['price'];
            }

            if ($hasStackable) {
                $data['stackable'] = false;
            }
            if ($hasMaxStack) {
                $data['max_stack'] = 1;
            }
            if ($hasIsConsumable) {
                $data['is_consumable'] = false;
            }

            if ($hasBonusesJson) {
                $data['bonuses_json'] = $item['bonuses'];
            }

            if ($hasEffectsJson) {
                $data['effects_json'] = [
                    'rarity' => $item['rarity'],
                    'description' => $item['description'],
                ];
            }

            if ($hasRarityId && isset($rarityMap[$item['rarity']])) {
                $data['rarity_id'] = $rarityMap[$item['rarity']];
            }

            if ($hasRarity) {
                $data['rarity'] = $item['rarity'];
            }

            if ($hasBonusStrength) {
                $data['bonus_strength'] = (int) ($item['bonuses']['strength'] ?? 0);
            }
            if ($hasBonusMagic) {
                $data['bonus_magic'] = (int) ($item['bonuses']['magic'] ?? 0);
            }
            if ($hasBonusDefense) {
                $data['bonus_defense'] = (int) ($item['bonuses']['defense'] ?? 0);
            }
            if ($hasBonusSpeed) {
                $data['bonus_speed'] = (int) ($item['bonuses']['speed'] ?? 0);
            }
            if ($hasBonusHp) {
                $data['bonus_hp'] = (int) ($item['bonuses']['hp'] ?? 0);
            }

            Item::updateOrCreate($key, $data);
        }

        // Pociones
        $potions = [
            [
                'name' => 'Poción de Curación',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'value_gold' => 50,
                'rarity' => 'common',
                'bonuses' => [],
            ],
            [
                'name' => 'Poción de Fuerza',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'bonuses' => [],
            ],
            [
                'name' => 'Poción de Magia',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'bonuses' => [],
            ],
            [
                'name' => 'Poción de Defensa',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'bonuses' => [],
            ],
            [
                'name' => 'Poción de Velocidad',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'bonuses' => [],
            ],
        ];

        foreach ($potions as $item) {
            $key = ['name' => $item['name']];
            $data = $item;
            unset($data['name'], $data['bonuses'], $data['rarity']);

            if ($hasRarityId && isset($rarityMap[$item['rarity']])) {
                $data['rarity_id'] = $rarityMap[$item['rarity']];
            }

            if ($hasRarity) {
                $data['rarity'] = $item['rarity'];
            }

            if ($hasBonusStrength) {
                $data['bonus_strength'] = (int) ($item['bonuses']['strength'] ?? 0);
            }
            if ($hasBonusMagic) {
                $data['bonus_magic'] = (int) ($item['bonuses']['magic'] ?? 0);
            }
            if ($hasBonusDefense) {
                $data['bonus_defense'] = (int) ($item['bonuses']['defense'] ?? 0);
            }
            if ($hasBonusSpeed) {
                $data['bonus_speed'] = (int) ($item['bonuses']['speed'] ?? 0);
            }
            if ($hasBonusHp) {
                $data['bonus_hp'] = (int) ($item['bonuses']['hp'] ?? 0);
            }

            Item::updateOrCreate($key, $data);
        }
    }
}
