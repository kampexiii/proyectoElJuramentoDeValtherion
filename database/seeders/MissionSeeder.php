<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FinalBoss;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Models\MissionReward;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        $missions = [
            [
                'slug' => 'senderos-del-umbral',
                'title' => 'Senderos del Umbral',
                'intro_text' => 'El umbral se abre y el camino se divide en sombras.',
                'context_text' => 'Cada eleccion acerca al enemigo final.',
                'repeatable' => false,
                'base_race_points' => 25,
                'boss_slug' => 'reina-del-umbral',
                'reward' => ['xp' => 120, 'gold' => 200, 'items_json' => null],
            ],
            [
                'slug' => 'ruinas-de-ceniza',
                'title' => 'Ruinas de Ceniza',
                'intro_text' => 'La ciudad enterrada aun guarda ecos de fuego.',
                'context_text' => 'El humo no deja ver el final del camino.',
                'repeatable' => true,
                'base_race_points' => 20,
                'boss_slug' => 'wyrm-de-ceniza',
                'reward' => ['xp' => 90, 'gold' => 150, 'items_json' => null],
            ],
            [
                'slug' => 'sal-del-destino',
                'title' => 'Sal del Destino',
                'intro_text' => 'Los salares se vuelven un laberinto blanco.',
                'context_text' => 'Solo los mas fuertes alcanzan el corazon del desierto.',
                'repeatable' => false,
                'base_race_points' => 30,
                'boss_slug' => 'titano-de-sal',
                'reward' => ['xp' => 150, 'gold' => 250, 'items_json' => null],
            ],
        ];

        foreach ($missions as $data) {
            $boss = FinalBoss::query()->where('slug', $data['boss_slug'])->first();
            if (!$boss) {
                continue;
            }

            $mission = Mission::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'intro_text' => $data['intro_text'],
                    'context_text' => $data['context_text'],
                    'status' => 'published',
                    'repeatable' => $data['repeatable'],
                    'base_race_points' => $data['base_race_points'],
                    'final_boss_id' => $boss->id,
                ]
            );

            MissionReward::query()->updateOrCreate(
                ['mission_id' => $mission->id],
                $data['reward']
            );

            $this->seedGraphForMission($mission);
        }
    }

    private function seedGraphForMission(Mission $mission): void
    {
        MissionChoice::query()->whereIn('mission_node_id', $mission->nodes()->pluck('id'))->delete();
        MissionNode::query()->where('mission_id', $mission->id)->delete();

        $stepNodes = [];

        $structure = [
            1 => ['Inicio del viaje'],
            2 => ['Sendero izquierdo', 'Sendero derecho'],
            3 => ['Puerta antigua', 'Puente roto'],
            4 => ['Torre en ruinas', 'Santuario oculto'],
            5 => ['El umbral'],
            6 => ['Sala del boss'],
        ];

        foreach ($structure as $step => $titles) {
            foreach ($titles as $index => $title) {
                $node = MissionNode::query()->create([
                    'mission_id' => $mission->id,
                    'step_index' => $step,
                    'is_start' => $step === 1 && $index === 0,
                    'title' => $title,
                    'body_text' => $title . ' te obliga a decidir.',
                ]);
                $stepNodes[$step][] = $node;
            }
        }

        foreach (range(1, 5) as $step) {
            $nextNodes = $stepNodes[$step + 1];
            foreach ($stepNodes[$step] as $node) {
                $this->createBranchChoices($node, $nextNodes);
            }
        }

        foreach ($stepNodes[6] as $node) {
            for ($i = 1; $i <= 3; $i++) {
                MissionChoice::query()->create([
                    'mission_node_id' => $node->id,
                    'choice_text' => 'Enfrentar al boss (' . $i . ')',
                    'outcome_text' => 'El combate final comienza.',
                    'difficulty_points' => $i % 4,
                    'effects_json' => null,
                    'next_node_id' => null,
                    'goes_to_boss' => true,
                    'order' => $i,
                ]);
            }
        }
    }

    private function createBranchChoices(MissionNode $node, array $nextNodes): void
    {
        $choices = [
            ['text' => 'Opcion A', 'difficulty' => 1],
            ['text' => 'Opcion B', 'difficulty' => 2],
            ['text' => 'Opcion C', 'difficulty' => 3],
        ];

        foreach ($choices as $index => $choice) {
            $nextNode = $nextNodes[$index % count($nextNodes)];
            MissionChoice::query()->create([
                'mission_node_id' => $node->id,
                'choice_text' => $choice['text'],
                'outcome_text' => 'Continuas por el camino elegido.',
                'difficulty_points' => $choice['difficulty'],
                'effects_json' => null,
                'next_node_id' => $nextNode->id,
                'goes_to_boss' => false,
                'order' => $index + 1,
            ]);
        }
    }
}
