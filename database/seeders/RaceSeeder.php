<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceSeeder extends Seeder
{
    public function run(): void
    {
        $races = [
            [
                'name' => 'Humanos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 5,
                'base_strength' => 5,
                'base_magic' => 5,
                'base_defense' => 5,
                'base_speed' => 5,
                'sprite' => 'Human.png',
            ],
            [
                'name' => 'Enanos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 6,
                'base_strength' => 6,
                'base_magic' => 1,
                'base_defense' => 9,
                'base_speed' => 3,
                'sprite' => 'Dwarf.png',
            ],
            [
                'name' => 'Altos Elfos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 3,
                'base_strength' => 4,
                'base_magic' => 8,
                'base_defense' => 3,
                'base_speed' => 7,
                'sprite' => 'HighElf.png',
            ],
            [
                'name' => 'Elfos Silvanos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 3,
                'base_strength' => 5,
                'base_magic' => 5,
                'base_defense' => 2,
                'base_speed' => 10,
                'sprite' => 'WoodElf.png',
            ],
            [
                'name' => 'Elfos Oscuros',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 3,
                'base_strength' => 7,
                'base_magic' => 4,
                'base_defense' => 2,
                'base_speed' => 9,
                'sprite' => 'DarkElf.png',
            ],
            [
                'name' => 'Orcos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 6,
                'base_strength' => 9,
                'base_magic' => 0,
                'base_defense' => 5,
                'base_speed' => 5,
                'sprite' => 'Orc.png',
            ],
            [
                'name' => 'Skaven',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 2,
                'base_strength' => 5,
                'base_magic' => 3,
                'base_defense' => 2,
                'base_speed' => 13,
                'sprite' => 'Skaven.png',
            ],
            [
                'name' => 'Hombres Bestia',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 4,
                'base_strength' => 8,
                'base_magic' => 1,
                'base_defense' => 4,
                'base_speed' => 8,
                'sprite' => 'Beastmen.png',
            ],
            [
                'name' => 'Condes Vampiro',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 3,
                'base_strength' => 5,
                'base_magic' => 7,
                'base_defense' => 2,
                'base_speed' => 8,
                'sprite' => 'VampireCounts.png',
            ],
            [
                'name' => 'Reyes Funerarios',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 4,
                'base_strength' => 5,
                'base_magic' => 5,
                'base_defense' => 4,
                'base_speed' => 7,
                'sprite' => 'TombKings.png',
            ],
            [
                'name' => 'Hombres Lagarto',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 5,
                'base_strength' => 6,
                'base_magic' => 2,
                'base_defense' => 7,
                'base_speed' => 5,
                'sprite' => 'Lizardmen.png',
            ],
            [
                'name' => 'Enanos del Caos',
                'min_role' => 'user',
                'stat_points_total' => 32,
                'base_hp' => 5,
                'base_strength' => 7,
                'base_magic' => 2,
                'base_defense' => 8,
                'base_speed' => 3,
                'sprite' => 'ChaosDwarf.png',
            ],
            // Premium
            [
                'name' => 'Demonios del Caos',
                'min_role' => 'premium',
                'stat_points_total' => 42,
                'base_hp' => 6,
                'base_strength' => 8,
                'base_magic' => 10,
                'base_defense' => 5,
                'base_speed' => 4,
                'sprite' => 'ChaosDaemon.png',
            ],
            // Admin-only
            [
                'name' => 'Señor Legendario del Caos',
                'min_role' => 'admin',
                'stat_points_total' => 52,
                'base_hp' => 9,
                'base_strength' => 12,
                'base_magic' => 7,
                'base_defense' => 9,
                'base_speed' => 3,
                'sprite' => 'assets/sprites/razas/Aldrik.png',
                'lore' => 'El Señor Legendario del Caos es el Elegido de la Grieta y el Caído. Su poder es legendario y solo está disponible para administradores.',
            ],
        ];

        foreach ($races as $race) {
            if ($race['name'] === 'Señor Legendario del Caos') {
                $existente = \DB::table('races')->where('name', 'Aldrik Vhar')->first();
                if ($existente) {
                    \DB::table('races')->where('id', $existente->id)->update(['name' => $race['name']]);
                }
            }

            $where = ['name' => $race['name']];
            $data = $race;
            unset($data['name']);

            // Si la columna lore no existe, no la uses
            if (!\Schema::hasColumn('races', 'lore')) {
                unset($data['lore']);
            }

            // min_role o access según esquema
            if (!\Schema::hasColumn('races', 'min_role') && \Schema::hasColumn('races', 'access')) {
                $data['access'] = $data['min_role'];
                unset($data['min_role']);
            }
            \DB::table('races')->updateOrInsert($where, $data);
        }
    }
}
