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

        $hasCode         = Schema::hasColumn('items', 'code');
        $hasSlot         = Schema::hasColumn('items', 'slot');
        $hasValueGold    = Schema::hasColumn('items', 'value_gold');
        $hasSellPrice    = Schema::hasColumn('items', 'sell_price');
        $hasBonusesJson  = Schema::hasColumn('items', 'bonuses_json');
        $hasEffectsJson  = Schema::hasColumn('items', 'effects_json');

        $hasBonusStrength = Schema::hasColumn('items', 'bonus_strength');
        $hasBonusMagic    = Schema::hasColumn('items', 'bonus_magic');
        $hasBonusDefense  = Schema::hasColumn('items', 'bonus_defense');
        $hasBonusSpeed    = Schema::hasColumn('items', 'bonus_speed');
        $hasBonusHp       = Schema::hasColumn('items', 'bonus_hp');

        $hasStackable     = Schema::hasColumn('items', 'stackable');
        $hasMaxStack      = Schema::hasColumn('items', 'max_stack');
        $hasIsConsumable  = Schema::hasColumn('items', 'is_consumable');

        $hasRarityId      = Schema::hasColumn('items', 'rarity_id');
        $hasRarity        = Schema::hasColumn('items', 'rarity');

        // =========================
        // Rarezas (si existe tabla)
        // =========================
        $rarityMap = [];
        if ($hasRarityId) {
            if (!Schema::hasTable('rarities')) {
                return;
            }

            $rarities = [
                'common' => 'Común',
                'rare'   => 'Rara',
                'epic'   => 'Épica',
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

        /*
         |======================================================================
         | REGLAS DE TIENDA
         |======================================================================
         | - Todo lo equipable SIEMPRE da bonus.
         | - common: +1 en 1 stat lógico
         | - rare:   +2 en 1 stat lógico  (y NOMBRE distinto del común: Reforzado/Superior/etc.)
         | - epic:   +3 en 1 stat o +2/+1 en 2 stats (y nombre con lore)
         |
         | - No se incluyen aquí ítems exclusivos de admin.
         */

        $items = [
            // ==========================================================
            // ARMADURAS Y CASCOS
            // ==========================================================
            [
                'code' => 'armor_head_vigia_paramo',
                'name' => 'Casco',
                'type' => 'armor',
                'slot' => 'helmet',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 60,
                'bonuses' => ['defense' => 1],
                'description' => 'Hierro simple. No es bonito, pero evita la desgracia.',
            ],
            [
                'code' => 'armor_head_reforzado',
                'name' => 'Casco Reforzado',
                'type' => 'armor',
                'slot' => 'helmet',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 140,
                'bonuses' => ['defense' => 2],
                'description' => 'Remaches y placas dobles. No te hace invencible, pero se nota.',
            ],
            [
                'code' => 'armor_chest_juramento_gris',
                'name' => 'Coraza Reforzada',
                'type' => 'armor',
                'slot' => 'armor',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 150,
                'bonuses' => ['defense' => 2],
                'description' => 'Coraza sólida. Hecha para resistir más de un asalto.',
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
                'description' => 'Hierro de muralla vieja. En el Bastión Negro, la piedra recuerda cada juramento roto.',
            ],

            // Armadura “de ataque” (pinchos): común (pega más que protege)
            [
                'code' => 'armor_chest_pinchos',
                'name' => 'Armadura de Pinchos',
                'type' => 'armor',
                'slot' => 'armor',
                'rarity' => 'common',
                'required_level' => 2,
                'price' => 85,
                'bonuses' => ['strength' => 1],
                'description' => 'Cuero con pinchos. No solo aguantas: también haces daño al contacto.',
            ],
            [
                'code' => 'armor_chest_sala_guerra',
                'name' => 'Armadura de la Sala de Guerra',
                'type' => 'armor',
                'slot' => 'armor',
                'rarity' => 'epic',
                'required_level' => 12,
                'price' => 340,
                'bonuses' => ['strength' => 2, 'defense' => 1],
                'description' => 'Forjada para quienes pisan la Sala de Guerra sin agachar la mirada.',
            ],

            // ==========================================================
            // ARMAS
            // ==========================================================
            [
                'code' => 'weapon_sword_filo_ceniza',
                'name' => 'Espada',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 70,
                'bonuses' => ['strength' => 1],
                'description' => 'Acero simple. Mata igual si el brazo no tiembla.',
            ],
            [
                'code' => 'weapon_bow_susurro_cuervo',
                'name' => 'Arco',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'common',
                'required_level' => 1,
                'price' => 75,
                'bonuses' => ['speed' => 1],
                'description' => 'Ligero y rápido. Golpea antes de que el enemigo piense.',
            ],
            [
                'code' => 'weapon_axe_rompejuramentos',
                'name' => 'Hacha Superior',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 160,
                'bonuses' => ['strength' => 2],
                'description' => 'Más pesada, mejor templada. Abre guardias y rompe líneas.',
            ],
            [
                'code' => 'weapon_dagger_colmillo_sombrio',
                'name' => 'Daga Reforzada',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 150,
                'bonuses' => ['speed' => 2],
                'description' => 'Filo más fino y estable. Entra rápido, sale limpio.',
            ],
            [
                'code' => 'weapon_staff_vara_grieta',
                'name' => 'Vara de la Grieta',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 320,
                'bonuses' => ['magic' => 3],
                'description' => 'La Grieta no regala poder. Lo cobra. Y aun así, algunos pagan.',
            ],
            [
                'code' => 'weapon_mace_martillo_alba',
                'name' => 'Maza del Bastión',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 300,
                'bonuses' => ['strength' => 3],
                'description' => 'No corta. Aplasta. En Valtherion, a veces eso basta.',
            ],
            [
                'code' => 'weapon_spear_guardia_valle',
                'name' => 'Lanza del Valle',
                'type' => 'weapon',
                'slot' => 'weapon',
                'rarity' => 'epic',
                'required_level' => 11,
                'price' => 330,
                'bonuses' => ['strength' => 2, 'speed' => 1],
                'description' => 'En los confines del Norte, quien llega tarde no llega.',
            ],

            // ==========================================================
            // ACCESORIOS
            // ==========================================================
            [
                'code' => 'acc_ring_sello_viejo',
                'name' => 'Anillo',
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
                'name' => 'Anillo Reforzado',
                'type' => 'accessory',
                'slot' => 'ring',
                'rarity' => 'rare',
                'required_level' => 5,
                'price' => 120,
                'bonuses' => ['defense' => 2],
                'description' => 'Metal más duro, sello más firme. Se nota cuando el golpe llega.',
            ],
            [
                'code' => 'acc_amulet_vela_negra',
                'name' => 'Talismán — La Vela Negra',
                'type' => 'accessory',
                'slot' => 'amulet',
                'rarity' => 'epic',
                'required_level' => 10,
                'price' => 260,
                'bonuses' => ['hp' => 2, 'defense' => 1],
                'description' => 'Dicen que alarga la vida… a cambio de algo que no se ve.',
            ],

            // ==========================================================
            // MONTURA
            // ==========================================================
            [
                'code' => 'mount_horse_corcel_guerra',
                'name' => 'Montura Entrenada',
                'type' => 'mount',
                'slot' => 'mount',
                'rarity' => 'rare',
                'required_level' => 8,
                'price' => 350,
                'bonuses' => ['speed' => 2],
                'description' => 'Más obediente, más rápida. En combate, eso es vida.',
            ],
        ];

        foreach ($items as $item) {
            $key = $hasCode ? ['code' => $item['code']] : ['name' => $item['name']];

            $data = [
                'name' => $item['name'],
                'type' => $item['type'],
                'required_level' => (int) $item['required_level'],
            ];

            if ($hasCode) {
                $data['code'] = $item['code'];
            }

            if ($hasSlot) {
                $data['slot'] = $item['slot'];
            }

            if ($hasValueGold) {
                $data['value_gold'] = (int) $item['price'];
            } elseif ($hasSellPrice) {
                $data['sell_price'] = (int) $item['price'];
            }

            // Equipables: no stack, no consumible
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
                $data['bonuses_json'] = $item['bonuses'] ?? [];
            }

            if ($hasEffectsJson) {
                $data['effects_json'] = [
                    'rarity' => $item['rarity'] ?? null,
                    'description' => $item['description'] ?? null,
                ];
            }

            if ($hasRarityId && isset($rarityMap[$item['rarity']])) {
                $data['rarity_id'] = $rarityMap[$item['rarity']];
            }
            if ($hasRarity) {
                $data['rarity'] = $item['rarity'];
            }

            $bonuses = $item['bonuses'] ?? [];

            if ($hasBonusStrength) {
                $data['bonus_strength'] = (int) ($bonuses['strength'] ?? 0);
            }
            if ($hasBonusMagic) {
                $data['bonus_magic'] = (int) ($bonuses['magic'] ?? 0);
            }
            if ($hasBonusDefense) {
                $data['bonus_defense'] = (int) ($bonuses['defense'] ?? 0);
            }
            if ($hasBonusSpeed) {
                $data['bonus_speed'] = (int) ($bonuses['speed'] ?? 0);
            }
            if ($hasBonusHp) {
                $data['bonus_hp'] = (int) ($bonuses['hp'] ?? 0);
            }

            Item::updateOrCreate($key, $data);
        }

        // ==========================================================
        // POCIONES (consumibles) — se definen con efectos
        // ==========================================================
        $potions = [
            [
                'code' => 'potion_heal_minor',
                'name' => 'Poción de Curación',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'max_stack' => 20,
                'is_consumable' => true,
                'value_gold' => 50,
                'rarity' => 'common',
                'effects' => ['heal_hp' => 5],
                'description' => 'Recupera una pequeña cantidad de vida.',
            ],
            [
                'code' => 'potion_strength_minor',
                'name' => 'Poción de Fuerza',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'max_stack' => 20,
                'is_consumable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'effects' => ['buff_strength' => 1, 'duration_turns' => 3],
                'description' => 'Aumenta la Fuerza temporalmente.',
            ],
            [
                'code' => 'potion_magic_minor',
                'name' => 'Poción de Magia',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'max_stack' => 20,
                'is_consumable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'effects' => ['buff_magic' => 1, 'duration_turns' => 3],
                'description' => 'Aumenta la Magia temporalmente.',
            ],
            [
                'code' => 'potion_defense_minor',
                'name' => 'Poción de Defensa',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'max_stack' => 20,
                'is_consumable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'effects' => ['buff_defense' => 1, 'duration_turns' => 3],
                'description' => 'Aumenta la Defensa temporalmente.',
            ],
            [
                'code' => 'potion_speed_minor',
                'name' => 'Poción de Velocidad',
                'type' => 'potion',
                'required_level' => 1,
                'stackable' => true,
                'max_stack' => 20,
                'is_consumable' => true,
                'value_gold' => 100,
                'rarity' => 'common',
                'effects' => ['buff_speed' => 1, 'duration_turns' => 3],
                'description' => 'Aumenta la Velocidad temporalmente.',
            ],
        ];

        foreach ($potions as $potion) {
            $key = $hasCode ? ['code' => $potion['code']] : ['name' => $potion['name']];

            $data = [
                'name' => $potion['name'],
                'type' => $potion['type'],
                'required_level' => (int) $potion['required_level'],
            ];

            if ($hasCode) {
                $data['code'] = $potion['code'];
            }

            if ($hasValueGold) {
                $data['value_gold'] = (int) $potion['value_gold'];
            } elseif ($hasSellPrice) {
                $data['sell_price'] = (int) $potion['value_gold'];
            }

            if ($hasStackable) {
                $data['stackable'] = true;
            }
            if ($hasMaxStack) {
                $data['max_stack'] = (int) ($potion['max_stack'] ?? 20);
            }
            if ($hasIsConsumable) {
                $data['is_consumable'] = true;
            }

            if ($hasEffectsJson) {
                $data['effects_json'] = [
                    'rarity' => $potion['rarity'],
                    'description' => $potion['description'],
                    'effects' => $potion['effects'],
                ];
            }

            if ($hasRarityId && isset($rarityMap[$potion['rarity']])) {
                $data['rarity_id'] = $rarityMap[$potion['rarity']];
            }
            if ($hasRarity) {
                $data['rarity'] = $potion['rarity'];
            }

            // Pociones: no son equipables (bonuses clásicos a 0)
            if ($hasBonusStrength) $data['bonus_strength'] = 0;
            if ($hasBonusMagic)    $data['bonus_magic'] = 0;
            if ($hasBonusDefense)  $data['bonus_defense'] = 0;
            if ($hasBonusSpeed)    $data['bonus_speed'] = 0;
            if ($hasBonusHp)       $data['bonus_hp'] = 0;

            Item::updateOrCreate($key, $data);
        }
    }
}
