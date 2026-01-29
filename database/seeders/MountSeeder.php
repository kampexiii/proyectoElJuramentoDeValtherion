<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MountSeeder extends Seeder
{
    public function run(): void
    {
        $ald = DB::table('races')->where('name', 'Señor Legendario del Caos')->first();

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

        $monturaNombre = 'Montura del Señor Legendario del Caos';
        $monturaAntigua = DB::table('mounts')->where('name', 'Montura de Aldrik Vhar')->first();
        if ($monturaAntigua) {
            DB::table('mounts')->where('id', $monturaAntigua->id)->update(['name' => $monturaNombre]);
        }

        DB::table('mounts')->updateOrInsert(
            ['name' => $monturaNombre],
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

        if (!Schema::hasTable('users') || !Schema::hasTable('characters') || !Schema::hasTable('races')) {
            return;
        }

        $adminUser = DB::table('users')->where('role', 'admin')->first();
        if (!$adminUser) {
            return;
        }

        $character = DB::table('characters')->where('user_id', $adminUser->id)->first();
        if (!$character) {
            return;
        }

        $race = DB::table('races')->where('id', $character->race_id)->first();
        if (!$race || !in_array($race->name, ['Señor Legendario del Caos', 'Aldrik Vhar'], true)) {
            return;
        }

        $mount = DB::table('mounts')->where('name', $monturaNombre)->first();
        if (!$mount) {
            return;
        }

        $maxStrength = (int) (DB::table('races')->max('base_strength') ?? 0);
        $maxMagic = (int) (DB::table('races')->max('base_magic') ?? 0);
        $maxDefense = (int) (DB::table('races')->max('base_defense') ?? 0);
        $maxSpeed = (int) (DB::table('races')->max('base_speed') ?? 0);

        $stats = [
            'fuerza' => min((int) $race->base_strength + (int) $mount->bonus_strength, $maxStrength),
            'magia' => min((int) $race->base_magic + (int) $mount->bonus_magic, $maxMagic),
            'defensa' => min((int) $race->base_defense + (int) $mount->bonus_defense, $maxDefense),
            'velocidad' => min((int) $race->base_speed + (int) $mount->bonus_speed, $maxSpeed),
        ];

        $data = [
            'mount_id' => $mount->id,
            'stats_json' => json_encode($stats),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('characters', 'has_mount')) {
            $data['has_mount'] = true;
        }

        DB::table('characters')->where('id', $character->id)->update($data);
    }
}
