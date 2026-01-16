<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class HeroesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Date::now();

        $chaosWarriorsRaceId = DB::table('races')->where('name', 'Guerreros del Caos')->value('id');
        if (!$chaosWarriorsRaceId) {
            return;
        }

        DB::table('heroes')->upsert([
            [
                'race_id' => $chaosWarriorsRaceId,
                'code' => 'archaon',
                'name' => 'Archaon',
                'description' => 'El Elegido de los Poderes Ruinosos. Un campeón único cuya mera presencia quiebra la moral enemiga. Su fuerza y defensa superan con claridad a cualquier guerrero común.',
                'base_hp' => 160,
                'base_strength' => 28,
                'base_magic' => 18,
                'base_defense' => 28,
                'base_speed' => 16,
                'unique_global' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['code'], ['race_id', 'name', 'description', 'base_hp', 'base_strength', 'base_magic', 'base_defense', 'base_speed', 'unique_global', 'updated_at']);
    }
}
