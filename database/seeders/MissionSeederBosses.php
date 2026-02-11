<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacterMissionRunStep;
use App\Models\FinalBoss;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Models\MissionReward;
use App\Services\Missions\MissionGraphValidator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MissionSeederBosses extends Seeder
{
    public function run(): void
    {
        $this->assertRequiredTables();
        $columns = $this->assertRequiredColumns();

        if (!class_exists(MissionGraphValidator::class)) {
            throw new RuntimeException('MissionGraphValidator no existe. No se puede validar misiones.');
        }

        $bosses = FinalBoss::query()
            ->select(['id', 'name', 'slug', 'sprite_path', 'base_stats_json'])
            ->orderBy('id')
            ->get();

        if ($bosses->count() !== 6) {
            $list = $bosses->map(fn ($boss) => sprintf('#%d %s (%s)', $boss->id, $boss->name, $boss->slug))->implode(', ');
            throw new RuntimeException('Se esperaban 6 bosses. Encontrados: ' . $bosses->count() . ' -> ' . $list);
        }

        $validator = app(MissionGraphValidator::class);
        $themes = $this->themes();

        foreach ($bosses as $index => $boss) {
            $theme = $themes[$index % count($themes)];
            $missionSlug = 'mision-' . $boss->slug;

            $mission = Mission::query()->updateOrCreate(
                ['slug' => $missionSlug],
                [
                    'title' => 'Mision: ' . $boss->name,
                    'intro_text' => $theme['intro'],
                    'context_text' => $theme['context'],
                    'status' => 'draft',
                    'repeatable' => false,
                    'base_race_points' => 20,
                    'final_boss_id' => $boss->id,
                ]
            );

            $this->resetMissionGraph($mission);

            $nodeMap = $this->createNodes($mission, $theme, $columns['mission_nodes']);
            $this->createChoices($nodeMap, $theme, $columns['mission_choices']);

            $rewardData = [
                'mission_id' => $mission->id,
                'xp' => 120,
                'gold' => 0,
            ];
            if ($columns['mission_rewards']['items_json']) {
                $rewardData['items_json'] = [];
            }

            MissionReward::query()->updateOrCreate(
                ['mission_id' => $mission->id],
                $rewardData
            );

            $mission->refresh()->load('reward');
            $errors = $validator->validate($mission);
            if (!empty($errors)) {
                $mission->update(['status' => 'draft']);
                throw new RuntimeException('Mision invalida (' . $missionSlug . '): ' . implode(' | ', $errors));
            }

            $mission->update(['status' => 'published']);
        }
    }

    private function assertRequiredTables(): void
    {
        $required = ['missions', 'mission_nodes', 'mission_choices', 'mission_rewards', 'final_bosses'];
        $missing = [];

        foreach ($required as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (!empty($missing)) {
            throw new RuntimeException('Faltan tablas requeridas: ' . implode(', ', $missing));
        }
    }

    /**
     * @return array<string, array<string, bool>>
     */
    private function assertRequiredColumns(): array
    {
        $missions = Schema::getColumnListing('missions');
        $nodes = Schema::getColumnListing('mission_nodes');
        $choices = Schema::getColumnListing('mission_choices');
        $rewards = Schema::getColumnListing('mission_rewards');

        $this->assertColumns('missions', $missions, ['slug', 'title', 'intro_text', 'status', 'repeatable', 'base_race_points', 'final_boss_id']);
        $this->assertColumns('mission_nodes', $nodes, ['mission_id', 'step_index', 'is_start', 'body_text']);
        $this->assertColumns('mission_choices', $choices, ['mission_node_id', 'choice_text', 'difficulty_points', 'order', 'goes_to_boss']);
        $this->assertColumns('mission_rewards', $rewards, ['mission_id', 'xp', 'gold']);

        if (!in_array('next_node_id', $choices, true)) {
            throw new RuntimeException('Falta columna mission_choices.next_node_id.');
        }
        if (!in_array('outcome_text', $choices, true)) {
            throw new RuntimeException('Falta columna mission_choices.outcome_text.');
        }

        return [
            'mission_nodes' => [
                'title' => in_array('title', $nodes, true),
                'body_text' => in_array('body_text', $nodes, true),
            ],
            'mission_choices' => [
                'effects_json' => in_array('effects_json', $choices, true),
            ],
            'mission_rewards' => [
                'items_json' => in_array('items_json', $rewards, true),
            ],
        ];
    }

    /**
     * @param string[] $columns
     * @param string[] $required
     */
    private function assertColumns(string $table, array $columns, array $required): void
    {
        $missing = array_diff($required, $columns);
        if (!empty($missing)) {
            throw new RuntimeException('Faltan columnas en ' . $table . ': ' . implode(', ', $missing));
        }
    }

    private function resetMissionGraph(Mission $mission): void
    {
        $nodeIds = $mission->nodes()->pluck('id');
        if ($nodeIds->isNotEmpty()) {
            $choiceIds = MissionChoice::query()
                ->whereIn('mission_node_id', $nodeIds)
                ->pluck('id');

            if ($choiceIds->isNotEmpty()) {
                CharacterMissionRunStep::query()->whereIn('choice_id', $choiceIds)->delete();
            }

            CharacterMissionRunStep::query()->whereIn('node_id', $nodeIds)->delete();
            MissionChoice::query()->whereIn('mission_node_id', $nodeIds)->delete();
        }

        MissionNode::query()->where('mission_id', $mission->id)->delete();
    }

    /**
     * @param array<string, array<string, bool>> $nodeColumns
     * @return array<string, MissionNode>
     */
    private function createNodes(Mission $mission, array $theme, array $nodeColumns): array
    {
        $nodeMap = [];
        foreach ($theme['steps'] as $stepIndex => $nodes) {
            foreach ($nodes as $index => $node) {
                $payload = [
                    'mission_id' => $mission->id,
                    'step_index' => $stepIndex,
                    'is_start' => $stepIndex === 1 && $index === 0,
                    'body_text' => $node['body'],
                ];

                if ($nodeColumns['title']) {
                    $payload['title'] = $node['title'];
                }

                $created = MissionNode::query()->create($payload);
                $nodeMap[$node['key']] = $created;
            }
        }

        return $nodeMap;
    }

    /**
     * @param array<string, MissionNode> $nodeMap
     * @param array<string, mixed> $theme
     * @param array<string, bool> $choiceColumns
     */
    private function createChoices(array $nodeMap, array $theme, array $choiceColumns): void
    {
        foreach ($theme['steps'] as $stepIndex => $nodes) {
            foreach ($nodes as $node) {
                $nodeModel = $nodeMap[$node['key']];
                $choices = $node['choices'];
                $ordered = $this->deterministicOrder($nodeModel->id, $choices);

                foreach ($ordered as $order => $choice) {
                    $payload = [
                        'mission_node_id' => $nodeModel->id,
                        'choice_text' => $choice['choice_text'],
                        'outcome_text' => $choice['outcome_text'],
                        'difficulty_points' => (int) $choice['difficulty_points'],
                        'goes_to_boss' => $stepIndex === 6,
                        'order' => $order,
                    ];

                    if ($choiceColumns['effects_json']) {
                        $payload['effects_json'] = (int) $choice['difficulty_points'] === 3 ? ['wounds' => 1] : null;
                    }

                    if ($stepIndex < 6) {
                        $payload['next_node_id'] = $nodeMap[$choice['next']]->id;
                    }

                    MissionChoice::query()->create($payload);
                }
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $choices
     * @return array<int, array<string, mixed>>
     */
    private function deterministicOrder(int $nodeId, array $choices): array
    {
        $scored = [];
        foreach ($choices as $choice) {
            $seed = crc32($nodeId . '|' . $choice['choice_text']);
            $scored[] = ['seed' => $seed, 'choice' => $choice];
        }

        usort($scored, fn ($a, $b) => $a['seed'] <=> $b['seed']);

        $ordered = [];
        $order = 1;
        foreach ($scored as $row) {
            $ordered[$order] = $row['choice'];
            $order++;
        }

        return $ordered;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function themes(): array
    {
        return [
            [
                'intro' => 'Los caminos a las montanas bajas no guardan silencio. La sombra del boss se siente antes de verlo.',
                'context' => 'Tu avance requiere decisiones duras antes del encuentro final.',
                'steps' => $this->buildSteps('exploracion', 'Senderos de piedra, grietas ocultas y un aire que recuerda juramentos rotos.'),
            ],
            [
                'intro' => 'Las cenizas guardan secretos bajo costras negras. Cada sala suena a ruina y deuda.',
                'context' => 'El rastro del boss exige explorar, asumir riesgos y resistir el desgaste.',
                'steps' => $this->buildSteps('caza', 'Huellas recientes, humo tenue y marcas de garras te obligan a perseguir con cautela.'),
            ],
            [
                'intro' => 'El umbral no perdona la duda. Las voces reclaman una decision antes de abrirse paso.',
                'context' => 'El boss no se enfrenta sin resolver el dilema de quienes custodian el paso.',
                'steps' => $this->buildSteps('intriga', 'Sellos rotos, juramentos velados y un pacto que puede volverse contra ti.'),
            ],
            [
                'intro' => 'El desierto de ceniza muerde el acero. Cada tramo exige aguante y disciplina.',
                'context' => 'Debes sobrevivir al desgaste antes de provocar al boss.',
                'steps' => $this->buildSteps('supervivencia', 'Viento cortante, agua escasa y un horizonte sin refugio.'),
            ],
            [
                'intro' => 'La ciudad fortificada no cede. El acceso al boss requiere quebrar la guardia externa.',
                'context' => 'El asedio define tu camino y tu fuerza antes del combate.',
                'steps' => $this->buildSteps('asedio', 'Murallas, patrullas y un porton reforzado que no admite errores.'),
            ],
            [
                'intro' => 'Una reliquia perdida exige decisiones rapidas. El boss protege el ultimo fragmento.',
                'context' => 'Debes asegurar la reliquia antes de que el boss cierre el paso.',
                'steps' => $this->buildSteps('reliquia', 'Runas antiguas, guardianes menores y un eco que llama por tu nombre.'),
            ],
        ];
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function buildSteps(string $theme, string $flavor): array
    {
        return [
            1 => [
                [
                    'key' => 's1',
                    'title' => 'Entrada del tramo',
                    'body' => $flavor . ' Tomas aire y eliges el primer camino.',
                    'choices' => $this->choicesFor($theme, 's1', ['s2a', 's2b']),
                ],
            ],
            2 => [
                [
                    'key' => 's2a',
                    'title' => 'Ruta del norte',
                    'body' => $flavor . ' La ruta norte exige disciplina y pasos medidos.',
                    'choices' => $this->choicesFor($theme, 's2a', ['s3a', 's3b']),
                ],
                [
                    'key' => 's2b',
                    'title' => 'Ruta del sur',
                    'body' => $flavor . ' La ruta sur promete rapidez, pero oculta riesgos.',
                    'choices' => $this->choicesFor($theme, 's2b', ['s3a', 's3b']),
                ],
            ],
            3 => [
                [
                    'key' => 's3a',
                    'title' => 'Cruce velado',
                    'body' => $flavor . ' Un cruce sin marcas obliga a leer el terreno.',
                    'choices' => $this->choicesFor($theme, 's3a', ['s4a', 's4b']),
                ],
                [
                    'key' => 's3b',
                    'title' => 'Pasaje estrecho',
                    'body' => $flavor . ' El pasaje estrecho comprime el paso y el aire.',
                    'choices' => $this->choicesFor($theme, 's3b', ['s4a', 's4b']),
                ],
            ],
            4 => [
                [
                    'key' => 's4a',
                    'title' => 'Puesto avanzado',
                    'body' => $flavor . ' Un puesto avanzado abandonado guarda senales del boss.',
                    'choices' => $this->choicesFor($theme, 's4a', ['s5']),
                ],
                [
                    'key' => 's4b',
                    'title' => 'Pasarela rota',
                    'body' => $flavor . ' La pasarela rota obliga a improvisar un cruce.',
                    'choices' => $this->choicesFor($theme, 's4b', ['s5']),
                ],
            ],
            5 => [
                [
                    'key' => 's5',
                    'title' => 'Camara de espera',
                    'body' => $flavor . ' El eco del boss se escucha cerca. Preparas el ultimo tramo.',
                    'choices' => $this->choicesFor($theme, 's5', ['s6']),
                ],
            ],
            6 => [
                [
                    'key' => 's6',
                    'title' => 'Umbral del boss',
                    'body' => $flavor . ' El boss esta a un paso. Cualquier duda pesa.',
                    'choices' => $this->choicesForBoss($theme),
                ],
            ],
        ];
    }

    /**
     * @param string[] $targets
     * @return array<int, array<string, mixed>>
     */
    private function choicesFor(string $theme, string $key, array $targets): array
    {
        return [
            [
                'choice_text' => 'Apretar el paso pese al riesgo',
                'outcome_text' => 'El avance es rapido, pero el esfuerzo te pasa factura.',
                'difficulty_points' => 3,
                'next' => $targets[0],
            ],
            [
                'choice_text' => 'Buscar un desvio tactico',
                'outcome_text' => 'El rodeo reduce amenazas, aunque consume tiempo.',
                'difficulty_points' => 1,
                'next' => $targets[1] ?? $targets[0],
            ],
            [
                'choice_text' => 'Inspeccionar el terreno antes de moverte',
                'outcome_text' => 'Detectas una ruta estable y avanzas con control.',
                'difficulty_points' => 0,
                'next' => $targets[0],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function choicesForBoss(string $theme): array
    {
        return [
            [
                'choice_text' => 'Entrar con determinacion',
                'outcome_text' => 'No hay vuelta atras. El combate comienza.',
                'difficulty_points' => 2,
            ],
            [
                'choice_text' => 'Preparar una apertura agresiva',
                'outcome_text' => 'Te expones para ganar ventaja inicial.',
                'difficulty_points' => 3,
            ],
            [
                'choice_text' => 'Buscar una posicion defensiva',
                'outcome_text' => 'Ajustas tu postura antes del choque.',
                'difficulty_points' => 1,
            ],
        ];
    }
}
