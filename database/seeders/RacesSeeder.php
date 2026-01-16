<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class RacesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Date::now();

        $races = [
            [
                'name' => 'Altos Elfos (Asur)',
                'access' => 'free',
                'lore' => 'Orgullosos y disciplinados, los Asur combinan tradición, arte y dominio arcano. Sus huestes destacan por la precisión, la magia refinada y una voluntad férrea frente al caos.',
                'base_hp' => 95,
                'base_strength' => 12,
                'base_magic' => 20,
                'base_defense' => 14,
                'base_speed' => 18,
            ],
            [
                'name' => 'Elfos Oscuros (Druchii)',
                'access' => 'free',
                'lore' => 'Crueles y ambiciosos, los Druchii prosperan en intrigas y golpes letales. Su poder nace del dolor, la velocidad y una magia oscura que castiga los errores del enemigo.',
                'base_hp' => 90,
                'base_strength' => 14,
                'base_magic' => 18,
                'base_defense' => 12,
                'base_speed' => 20,
            ],
            [
                'name' => 'Elfos Silvanos (Asray)',
                'access' => 'free',
                'lore' => 'Guardianes de bosques antiguos, los Asray libran una guerra de emboscadas y flechas. Su fuerza está en la movilidad, el sigilo y el vínculo con espíritus del bosque.',
                'base_hp' => 92,
                'base_strength' => 13,
                'base_magic' => 16,
                'base_defense' => 12,
                'base_speed' => 22,
            ],
            [
                'name' => 'Enanos',
                'access' => 'free',
                'lore' => 'Resistentes y testarudos, los Enanos dominan la forja y la guerra defensiva. Su metal aguanta, sus hachas golpean fuerte y su experiencia compensa su menor movilidad.',
                'base_hp' => 120,
                'base_strength' => 18,
                'base_magic' => 6,
                'base_defense' => 22,
                'base_speed' => 10,
            ],
            [
                'name' => 'Humanos (Imperio/Bretonia/Kislev)',
                'access' => 'free',
                'lore' => 'Versátiles y resilientes, los Humanos se adaptan a cualquier frente. Entre acero, pólvora, fe y disciplina, su equilibrio los convierte en un pilar contra amenazas sobrenaturales.',
                'base_hp' => 105,
                'base_strength' => 15,
                'base_magic' => 12,
                'base_defense' => 15,
                'base_speed' => 15,
            ],
            [
                'name' => 'Orcos y Goblins',
                'access' => 'free',
                'lore' => 'Caóticos e impredecibles, viven para la pelea. Los Orcos aportan brutalidad y resistencia; los Goblins, trampas y astucia. Su fuerza crece con la batalla.',
                'base_hp' => 125,
                'base_strength' => 20,
                'base_magic' => 8,
                'base_defense' => 14,
                'base_speed' => 14,
            ],
            [
                'name' => 'Hombres Bestia',
                'access' => 'free',
                'lore' => 'Engendros salvajes de bosques oscuros, atacan desde la maleza con furia primaria. Su estilo es directo, con golpes potentes y empuje constante.',
                'base_hp' => 118,
                'base_strength' => 19,
                'base_magic' => 9,
                'base_defense' => 13,
                'base_speed' => 16,
            ],
            [
                'name' => 'Skaven',
                'access' => 'free',
                'lore' => 'Las ratas del subsuelo se multiplican en sombras, traiciones y tecnología peligrosa. Rápidos y astutos, compensan su fragilidad con venenos, números y artimañas.',
                'base_hp' => 88,
                'base_strength' => 12,
                'base_magic' => 14,
                'base_defense' => 10,
                'base_speed' => 23,
            ],
            [
                'name' => 'Reyes Funerarios',
                'access' => 'free',
                'lore' => 'Reinos de polvo y eternidad, sus legiones no sienten miedo ni fatiga. Su poder reside en la disciplina ancestral, resistencia y magia ritual ligada a tumba y desierto.',
                'base_hp' => 110,
                'base_strength' => 16,
                'base_magic' => 14,
                'base_defense' => 17,
                'base_speed' => 12,
            ],
            [
                'name' => 'Hombres Lagarto',
                'access' => 'free',
                'lore' => 'Forjados para cumplir designios antiguos, combinan fuerza reptiliana con disciplina fría. Sus guerreros soportan el castigo y sus sabios canalizan poderes primordiales.',
                'base_hp' => 130,
                'base_strength' => 18,
                'base_magic' => 16,
                'base_defense' => 18,
                'base_speed' => 12,
            ],
            [
                'name' => 'Ogros',
                'access' => 'free',
                'lore' => 'Gigantes hambrientos, aplastan líneas con pura masa y ferocidad. Su magia es instintiva y su defensa nace de su dureza natural y armaduras improvisadas.',
                'base_hp' => 140,
                'base_strength' => 22,
                'base_magic' => 10,
                'base_defense' => 16,
                'base_speed' => 12,
            ],
            [
                'name' => 'Condes Vampiro',
                'access' => 'free',
                'lore' => 'Señores inmortales que gobiernan la noche. Su poder mezcla magia necromántica y fuerza sobrenatural, con defensas alimentadas por la voluntad y la sangre.',
                'base_hp' => 112,
                'base_strength' => 17,
                'base_magic' => 18,
                'base_defense' => 14,
                'base_speed' => 16,
            ],
            [
                'name' => 'Demonios del Caos',
                'access' => 'premium',
                'lore' => 'Manifestaciones del caos puro, existen para corromper y destruir. Su presencia distorsiona la realidad: magia feroz, golpes imprevisibles y resistencia antinatural.',
                'base_hp' => 115,
                'base_strength' => 18,
                'base_magic' => 22,
                'base_defense' => 14,
                'base_speed' => 18,
            ],
            [
                'name' => 'Guerreros del Caos',
                'access' => 'admin',
                'lore' => 'Veteranos endurecidos por el norte, juramentados a poderes oscuros. Su armadura es casi impenetrable y su fuerza implacable, a costa de una magia menos refinada.',
                'base_hp' => 135,
                'base_strength' => 22,
                'base_magic' => 12,
                'base_defense' => 22,
                'base_speed' => 12,
            ],
        ];

        $payload = array_map(function (array $race) use ($now) {
            return array_merge($race, ['created_at' => $now, 'updated_at' => $now]);
        }, $races);

        DB::table('races')->upsert(
            $payload,
            ['name'],
            ['access', 'lore', 'base_hp', 'base_strength', 'base_magic', 'base_defense', 'base_speed', 'updated_at']
        );
    }
}
