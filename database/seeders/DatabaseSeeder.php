<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\EquipmentSlotsSeeder;
use Database\Seeders\HeroesSeeder;
use Database\Seeders\LootBaseSeeder;
use Database\Seeders\RacesSeeder;
use Database\Seeders\RaritiesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RaritiesSeeder::class,
            RacesSeeder::class,
            EquipmentSlotsSeeder::class,
            HeroesSeeder::class,
            LootBaseSeeder::class,
            AdminUserSeeder::class,
        ]);

        if (!User::query()->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}

