<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MountSeeder extends Seeder
{
    public function run(): void
    {
        $ald = DB::table('races')->where('name', 'Aldrik Vhar')->first();

        $maxStrength = (int) (DB::table('races')->max('base_strength') ?? 0);
        $maxMagic = (int) (DB::table('races')->max('base_magic') ?? 0);
        $maxDefense = (int) (DB::table('races')->max('base_defense') ?? 0);
        $maxSpeed = (int) (DB::table('races')->max('base_speed') ?? 0);

        $bonusStrength = 0;
        $bonusMagic = 0;
        $bonusDefense = 0;
        $bonusSpeed = 0;

        if ($ald) {
            $bonusStrength = max($maxStrength - (int) $ald->base_strength, 0);
            $bonusMagic = max($maxMagic - (int) $ald->base_magic, 0);
            $bonusDefense = max($maxDefense - (int) $ald->base_defense, 0);
            $bonusSpeed = max($maxSpeed - (int) $ald->base_speed, 0);
        }

        DB::table('mounts')->updateOrInsert(
            ['name' => 'Montura de Aldrik Vhar'],
            [
                'bonus_strength' => $bonusStrength,
                'bonus_magic' => $bonusMagic,
                'bonus_defense' => $bonusDefense,
                'bonus_speed' => $bonusSpeed,
                'is_admin_fixed' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
