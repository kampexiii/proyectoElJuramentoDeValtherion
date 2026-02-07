<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FinalBoss;
use Illuminate\Database\Seeder;

class FinalBossSeeder extends Seeder
{
    public function run(): void
    {
        $bosses = [
            [
                'name' => 'Wyrm de Ceniza',
                'slug' => 'wyrm-de-ceniza',
                'lore' => 'Un dragon antiguo que habita bajo las ruinas volcadas.',
                'base_stats_json' => ['hp' => 1200, 'damage' => 120, 'defense' => 60],
            ],
            [
                'name' => 'Titano de Sal',
                'slug' => 'titano-de-sal',
                'lore' => 'Una colosal estatua viva que protege los salares sagrados.',
                'base_stats_json' => ['hp' => 1600, 'damage' => 95, 'defense' => 90],
            ],
            [
                'name' => 'Reina del Umbral',
                'slug' => 'reina-del-umbral',
                'lore' => 'Soberana del vacio entre mundos, astuta y peligrosa.',
                'base_stats_json' => ['hp' => 1100, 'damage' => 140, 'defense' => 50],
            ],
        ];

        foreach ($bosses as $boss) {
            $boss['sprite_path'] = '/assets/bosses/' . $boss['slug'] . '.png';
            FinalBoss::query()->updateOrCreate(
                ['slug' => $boss['slug']],
                $boss
            );
        }
    }
}
