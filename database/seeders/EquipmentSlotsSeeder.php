<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class EquipmentSlotsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Date::now();

        DB::table('equipment_slots')->upsert([
            ['code' => 'head', 'name' => 'Cabeza', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'chest', 'name' => 'Pecho', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'legs', 'name' => 'Piernas', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'weapon', 'name' => 'Arma', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'accessory1', 'name' => 'Accesorio 1', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'accessory2', 'name' => 'Accesorio 2', 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['name', 'updated_at']);
    }
}
