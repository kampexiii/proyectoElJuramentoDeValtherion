<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class RaritiesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Date::now();

        DB::table('rarities')->upsert([
            ['name' => 'common', 'weight' => 700, 'min_level' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'rare', 'weight' => 250, 'min_level' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'epic', 'weight' => 45, 'min_level' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'legendary', 'weight' => 5, 'min_level' => 40, 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['weight', 'min_level', 'updated_at']);
    }
}
