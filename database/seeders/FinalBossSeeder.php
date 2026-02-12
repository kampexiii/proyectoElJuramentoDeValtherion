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
                'name' => "Oggs' El Troll Bajo La Montaña",
                'slug' => 'oggs-el-troll-bajo-la-montana',
                'lore' => "Bajo la Montaña Vieja, donde la piedra suda humedad y el eco muerde, vive Oggs'.\n"
                    . "No protege un tesoro: protege el camino.\n"
                    . "Cada viajero que baja sin respeto deja algo atrás: una bolsa, una mano... o un nombre.\n"
                    . "Dicen que su piel es roca viva y que sus huesos crujen como puertas de cripta.",
                'base_stats_json' => ['hp' => 420, 'damage' => 38, 'defense' => 22],
            ],
            [
                'name' => 'Reina del Umbral',
                'slug' => 'reina-del-umbral',
                'lore' => "Soberana del vacío entre mundos, astuta y peligrosa.\n"
                    . "No negocia: mide tu voluntad y la rompe donde más duele.",
                'base_stats_json' => ['hp' => 1100, 'damage' => 140, 'defense' => 50],
            ],
            [
                'name' => 'Sistera Nhal, la Guardiana del Umbral',
                'slug' => 'sistera-nhal-la-guardiana-del-umbral',
                'lore' => "Custodia el Umbral donde el mundo se afila.\n"
                    . "No mata por odio, sino por orden.\n"
                    . "Sus plegarias son cadenas y su mirada, sentencia.",
                'base_stats_json' => ['hp' => 560, 'damage' => 24, 'defense' => 35],
            ],
            [
                'name' => 'Titano de Sal',
                'slug' => 'titano-de-sal',
                'lore' => "Una colosal estatua viva que protege los salares sagrados.\n"
                    . "Su paso hace temblar la costra blanca y su aliento reseca la sangre.",
                'base_stats_json' => ['hp' => 1600, 'damage' => 95, 'defense' => 90],
            ],
            [
                'name' => 'Varkh, el Colmillo de Ceniza',
                'slug' => 'varkh-el-colmillo-de-ceniza',
                'lore' => "Nació entre humo y huesos, y aprendió a cazar donde otros rezan.\n"
                    . "Varkh no ruge: susurra. Y cuando lo oyes, ya es tarde.\n"
                    . "Su acero está manchado de hollín y juramentos rotos.",
                'base_stats_json' => ['hp' => 352, 'damage' => 52, 'defense' => 14],
            ],
            [
                'name' => 'Wyrm de Ceniza',
                'slug' => 'wyrm-de-ceniza',
                'lore' => "Un dragón antiguo que habita bajo las ruinas volcadas.\n"
                    . "Su aliento no quema: sepulta. Su sombra no pasa: se queda.",
                'base_stats_json' => ['hp' => 1200, 'damage' => 120, 'defense' => 60],
            ],
        ];

        foreach ($bosses as $boss) {
            $boss['sprite_path'] = '/assets/sprites/bosses/' . $boss['slug'] . '.png';

            FinalBoss::query()->updateOrCreate(
                ['slug' => $boss['slug']],
                $boss
            );
        }
    }
}
