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
use Illuminate\Support\Str;
use RuntimeException;

class MissionSeeder extends Seeder
{
    private bool $hasChoiceEffectsJson = false;
    private bool $hasRewardItemsJson = false;

    public function run(): void
    {
        $this->hasChoiceEffectsJson = Schema::hasColumn('mission_choices', 'effects_json');
        $this->hasRewardItemsJson = Schema::hasColumn('mission_rewards', 'items_json');

        $bosses = FinalBoss::query()
            ->orderBy('id')
            ->get(['id', 'name', 'slug']);

        if ($bosses->count() !== 6) {
            throw new RuntimeException(
                'Se esperaban EXACTAMENTE 6 bosses en final_bosses. Encontrados: ' . $bosses->count()
            );
        }

        $missions = $this->missionDefinitions($bosses->all());
        $validator = app(MissionGraphValidator::class);

        foreach ($missions as $data) {
            $boss = FinalBoss::query()->where('slug', $data['boss_slug'])->first();
            if (!$boss) {
                throw new RuntimeException('Boss no encontrado para la misión: ' . $data['slug']);
            }

            // Crear/actualizar en draft; solo se publica si pasa validación.
            $mission = Mission::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'intro_text' => $data['intro_text'],
                    'context_text' => $data['context_text'],
                    'status' => 'draft',
                    'repeatable' => (bool) $data['repeatable'],
                    'base_race_points' => (int) $data['base_race_points'],
                    'final_boss_id' => $boss->id,
                ]
            );

            MissionReward::query()->updateOrCreate(
                ['mission_id' => $mission->id],
                $this->normalizeRewardPayload($data['reward'])
            );

            $this->seedGraphForMission($mission, $data['graph']);

            $mission->refresh()->load('reward');

            $errors = $validator->validate($mission);
            if (!empty($errors)) {
                $mission->update(['status' => 'draft']);
                throw new RuntimeException('Misión inválida (' . $mission->slug . '): ' . implode(' | ', $errors));
            }

            $mission->update(['status' => 'published']);
        }
    }

    private function normalizeRewardPayload(array $reward): array
    {
        $payload = [
            'xp' => (int) ($reward['xp'] ?? 0),
            'gold' => (int) ($reward['gold'] ?? 0),
        ];

        if ($this->hasRewardItemsJson) {
            $payload['items_json'] = $reward['items_json'] ?? null;
        }

        return $payload;
    }

    private function seedGraphForMission(Mission $mission, array $graph): void
    {
        $existingNodeIds = $mission->nodes()->pluck('id');
        if ($existingNodeIds->isNotEmpty()) {
            $existingChoiceIds = MissionChoice::query()
                ->whereIn('mission_node_id', $existingNodeIds)
                ->pluck('id');

            if ($existingChoiceIds->isNotEmpty()) {
                CharacterMissionRunStep::query()
                    ->whereIn('choice_id', $existingChoiceIds)
                    ->delete();
            }

            CharacterMissionRunStep::query()
                ->whereIn('node_id', $existingNodeIds)
                ->delete();

            MissionChoice::query()->whereIn('mission_node_id', $existingNodeIds)->delete();
        }

        MissionNode::query()->where('mission_id', $mission->id)->delete();

        $stepNodes = [];

        foreach ($graph['steps'] as $step => $nodes) {
            foreach ($nodes as $index => $nodeData) {
                $node = MissionNode::query()->create([
                    'mission_id' => $mission->id,
                    'step_index' => (int) $step,
                    'is_start' => ((int) $step === 1 && (int) $index === 0),
                    'title' => (string) $nodeData['title'],
                    'body_text' => (string) $nodeData['body'],
                ]);

                $stepNodes[(int) $step][(string) $nodeData['key']] = $node;
            }
        }

        foreach ($graph['steps'] as $step => $nodes) {
            foreach ($nodes as $nodeData) {
                $node = $stepNodes[(int) $step][(string) $nodeData['key']];

                if ((int) $step < 6) {
                    $this->createBranchChoices(
                        $node,
                        (array) $nodeData['choices'],
                        $stepNodes[((int) $step) + 1],
                        (string) $nodeData['key']
                    );
                } else {
                    $this->createBossChoices(
                        $node,
                        (array) $nodeData['choices'],
                        (string) $nodeData['key']
                    );
                }
            }
        }
    }

    /**
     * Orden de opciones determinista (para que NO se intuya dificultad) y estable entre seeds.
     * Se basa en: step + nodeKey + choice_text + outcome_text
     */
    private function deterministicOrderMap(MissionNode $node, string $nodeKey, array $choices): array
    {
        $seedBase = (string) $node->step_index . '|' . $nodeKey . '|' . $node->title;

        $indexed = [];
        foreach ($choices as $idx => $choice) {
            $choiceText = (string) ($choice['text'] ?? '');
            $outcomeText = (string) ($choice['outcome'] ?? '');
            $hash = sprintf('%u', crc32($seedBase . '|' . $choiceText . '|' . $outcomeText));
            $indexed[] = ['idx' => (int) $idx, 'hash' => $hash];
        }

        usort($indexed, static fn ($a, $b) => $a['hash'] <=> $b['hash']);

        $orderMap = [];
        $order = 1;
        foreach ($indexed as $row) {
            $orderMap[(int) $row['idx']] = $order;
            $order++;
        }

        return $orderMap;
    }

    private function createBranchChoices(MissionNode $node, array $choices, array $nextNodes, string $nodeKey): void
    {
        $orderMap = $this->deterministicOrderMap($node, $nodeKey, $choices);

        foreach ($choices as $index => $choice) {
            $nextKey = (string) ($choice['next'] ?? '');
            $nextNode = $nextNodes[$nextKey] ?? null;

            if (!$nextNode) {
                throw new RuntimeException('Nodo siguiente no encontrado para el nodo #' . $node->id . '.');
            }

            $difficulty = (int) ($choice['difficulty'] ?? 0);
            $payload = [
                'mission_node_id' => $node->id,
                'choice_text' => (string) $choice['text'],
                'outcome_text' => (string) $choice['outcome'],
                'difficulty_points' => $difficulty,
                'next_node_id' => $nextNode->id,
                'goes_to_boss' => false,
                'order' => $orderMap[(int) $index] ?? ((int) $index + 1),
            ];

            if ($this->hasChoiceEffectsJson) {
                $payload['effects_json'] = $difficulty === 3 ? ['wounds' => 1] : null;
            }

            MissionChoice::query()->create($payload);
        }
    }

    private function createBossChoices(MissionNode $node, array $choices, string $nodeKey): void
    {
        $orderMap = $this->deterministicOrderMap($node, $nodeKey, $choices);

        foreach ($choices as $index => $choice) {
            $difficulty = (int) ($choice['difficulty'] ?? 0);

            $payload = [
                'mission_node_id' => $node->id,
                'choice_text' => (string) $choice['text'],
                'outcome_text' => (string) $choice['outcome'],
                'difficulty_points' => $difficulty,
                'next_node_id' => null,
                'goes_to_boss' => true,
                'order' => $orderMap[(int) $index] ?? ((int) $index + 1),
            ];

            if ($this->hasChoiceEffectsJson) {
                $payload['effects_json'] = $difficulty === 3 ? ['wounds' => 1] : null;
            }

            MissionChoice::query()->create($payload);
        }
    }

    /**
     * 6 misiones (1 por boss). Si existen los 3 bosses “clásicos” por slug, se usan las misiones ya diseñadas.
     * Para los otros bosses, se generan misiones nuevas con grafo válido y tono Valtherion.
     *
     * @param array<int, FinalBoss> $bosses
     */
    private function missionDefinitions(array $bosses): array
    {
        $bySlug = [];
        foreach ($bosses as $b) {
            $bySlug[$b->slug] = $b;
        }

        $defs = [];

        // Misiones “clásicas” si existen esos slugs.
        if (isset($bySlug['wyrm-de-ceniza'])) {
            $defs[] = $this->missionRuinasDeCeniza();
        }
        if (isset($bySlug['reina-del-umbral'])) {
            $defs[] = $this->missionSenderosDelUmbral();
        }
        if (isset($bySlug['titano-de-sal'])) {
            $defs[] = $this->missionSalDelDestino();
        }

        // Para el resto de bosses (hasta completar 6), generar misiones nuevas.
        $usedBossSlugs = array_map(static fn ($d) => $d['boss_slug'], $defs);
        $remaining = array_values(array_filter($bosses, static fn (FinalBoss $b) => !in_array($b->slug, $usedBossSlugs, true)));

        $templates = [
            'catedral-del-juramento',
            'bosque-de-la-escarcha-negra',
            'foso-de-hierro-y-sangre',
        ];

        $i = 0;
        foreach ($remaining as $boss) {
            $template = $templates[$i % count($templates)];
            $defs[] = $this->genericMissionForBoss($boss, $template);
            $i++;
        }

        // Seguridad: exactamente 6 misiones (1 por boss)
        if (count($defs) !== 6) {
            throw new RuntimeException('El seeder debe generar EXACTAMENTE 6 misiones. Generadas: ' . count($defs));
        }

        // Seguridad: no repetir boss
        $bossSlugs = array_map(static fn ($d) => $d['boss_slug'], $defs);
        if (count(array_unique($bossSlugs)) !== 6) {
            throw new RuntimeException('Hay bosses repetidos en las definiciones de misión.');
        }

        return $defs;
    }

    private function missionRuinasDeCeniza(): array
    {
        return [
            'slug' => 'ruinas-de-ceniza',
            'title' => 'Ruinas de Ceniza',
            'intro_text' => 'Dicen que la ceniza guarda memoria. Aquí, cada pared aún repite el último grito.',
            'context_text' => 'Sigues una traza antigua de juramentos rotos hacia el corazón calcinado donde repta el Wyrm.',
            'repeatable' => true,
            'base_race_points' => 20,
            'boss_slug' => 'wyrm-de-ceniza',
            'reward' => ['xp' => 110, 'gold' => 180, 'items_json' => null],
            'graph' => [
                'steps' => [
                    1 => [
                        [
                            'key' => 'r1',
                            'title' => 'Pórtico hundido',
                            'body' => 'El pórtico cruje bajo el peso de siglos. La ceniza sube en remolinos y te seca la garganta.',
                            'choices' => [
                                ['text' => 'Deslizarte por una grieta estrecha', 'outcome' => 'Ganas altura, pero el metal te muerde la piel.', 'difficulty' => 2, 'next' => 'r2a'],
                                ['text' => 'Forzar la puerta oxidada', 'outcome' => 'El hierro cede con un chillido… y te llevas parte del precio.', 'difficulty' => 3, 'next' => 'r2b'],
                                ['text' => 'Rodear por arcadas caídas', 'outcome' => 'Es más largo, pero el suelo responde firme.', 'difficulty' => 1, 'next' => 'r2a'],
                                ['text' => 'Esperar a que baje el humo', 'outcome' => 'Pierdes tiempo, ganas aire. No siempre se puede correr.', 'difficulty' => 0, 'next' => 'r2b'],
                            ],
                        ],
                    ],
                    2 => [
                        [
                            'key' => 'r2a',
                            'title' => 'Galería de vidrio',
                            'body' => 'Paneles quebrados cuelgan como colmillos. Cada paso suena a un incendio que aún no termina.',
                            'choices' => [
                                ['text' => 'Cruzar por vigas de cristal', 'outcome' => 'El suelo vibra, pero tu pulso manda.', 'difficulty' => 2, 'next' => 'r3a'],
                                ['text' => 'Bajar por el pozo de ventilación', 'outcome' => 'El descenso es brutal. Sales con el pecho ardiendo.', 'difficulty' => 3, 'next' => 'r3b'],
                                ['text' => 'Seguir marcas antiguas de guardia', 'outcome' => 'Alguien supo huir. Tú sabes leerlo.', 'difficulty' => 1, 'next' => 'r3a'],
                                ['text' => 'Avanzar lento, sin ruido', 'outcome' => 'La paciencia te compra estabilidad.', 'difficulty' => 0, 'next' => 'r3b'],
                            ],
                        ],
                        [
                            'key' => 'r2b',
                            'title' => 'Taller sepultado',
                            'body' => 'Herramientas calcinadas asoman del polvo. Bajo los escombros… algo respira.',
                            'choices' => [
                                ['text' => 'Buscar herramientas útiles', 'outcome' => 'Encuentras hierro, pero pagas en tiempo.', 'difficulty' => 1, 'next' => 'r3b'],
                                ['text' => 'Correr por la zona inestable', 'outcome' => 'El techo cae. Te salva el instinto y una herida.', 'difficulty' => 3, 'next' => 'r3a'],
                                ['text' => 'Atajo por un canal estrecho', 'outcome' => 'Te roza la piedra, pero te deja pasar.', 'difficulty' => 2, 'next' => 'r3b'],
                                ['text' => 'Enfriar brasas con arena', 'outcome' => 'El suelo deja de morder. Avanzas firme.', 'difficulty' => 0, 'next' => 'r3a'],
                            ],
                        ],
                    ],
                    3 => [
                        [
                            'key' => 'r3a',
                            'title' => 'Anfiteatro rojo',
                            'body' => 'El escenario está fundido en rojo oscuro. Aquí, el silencio pesa como culpa.',
                            'choices' => [
                                ['text' => 'Subir gradas quemadas', 'outcome' => 'Ganas altura. Ves la ruta… y el peligro.', 'difficulty' => 1, 'next' => 'r4a'],
                                ['text' => 'Explorar el palco derruido', 'outcome' => 'Encuentras un paso oculto entre huesos de madera.', 'difficulty' => 2, 'next' => 'r4a'],
                                ['text' => 'Cortar por el foso central', 'outcome' => 'El calor te desgasta. No te regala nada.', 'difficulty' => 3, 'next' => 'r4a'],
                                ['text' => 'Parar y reajustar equipo', 'outcome' => 'Ordenas tu guerra. El caos no decide por ti.', 'difficulty' => 0, 'next' => 'r4a'],
                            ],
                        ],
                        [
                            'key' => 'r3b',
                            'title' => 'Cámara de ceniza',
                            'body' => 'Ceniza hasta los tobillos. Señales borrosas en la pared como juramentos deshechos.',
                            'choices' => [
                                ['text' => 'Seguir el humo frío', 'outcome' => 'La corriente marca el rumbo con precisión.', 'difficulty' => 2, 'next' => 'r4a'],
                                ['text' => 'Forzar una compuerta sellada', 'outcome' => 'Cede… y te cobra con metal y sangre.', 'difficulty' => 3, 'next' => 'r4a'],
                                ['text' => 'Leer símbolos de evacuación', 'outcome' => 'Te enseñan una salida estable.', 'difficulty' => 1, 'next' => 'r4a'],
                                ['text' => 'Esperar a que asiente el polvo', 'outcome' => 'Ves mejor. A veces eso basta.', 'difficulty' => 0, 'next' => 'r4a'],
                            ],
                        ],
                    ],
                    4 => [
                        [
                            'key' => 'r4a',
                            'title' => 'Biblioteca quemada',
                            'body' => 'Manuscritos pegados a piedra. Dos rutas. Ninguna limpia.',
                            'choices' => [
                                ['text' => 'Bajar por escaleras rotas al foso', 'outcome' => 'Desciendes entre tablones que no prometen nada.', 'difficulty' => 2, 'next' => 'r5a'],
                                ['text' => 'Pasadizo de monjes', 'outcome' => 'Estrecho, seguro. Casi demasiado.', 'difficulty' => 1, 'next' => 'r5b'],
                                ['text' => 'Atravesar sala de brasas', 'outcome' => 'El calor te marca como un sello.', 'difficulty' => 3, 'next' => 'r5a'],
                                ['text' => 'Rodeo por claustro exterior', 'outcome' => 'Evitas el fuego directo, no el destino.', 'difficulty' => 0, 'next' => 'r5b'],
                            ],
                        ],
                    ],
                    5 => [
                        [
                            'key' => 'r5a',
                            'title' => 'Foso de brasas',
                            'body' => 'El suelo vibra. El aire late. El Wyrm duerme cerca, y sueña con hambre.',
                            'choices' => [
                                ['text' => 'Descender por cadenas oxidadas', 'outcome' => 'Aguanta… por ahora.', 'difficulty' => 2, 'next' => 'r6'],
                                ['text' => 'Saltar entre pilares calientes', 'outcome' => 'Avanzas rápido. Pagas caro.', 'difficulty' => 3, 'next' => 'r6'],
                                ['text' => 'Buscar un puente antiguo', 'outcome' => 'El paso es más seguro de lo que parece.', 'difficulty' => 1, 'next' => 'r6'],
                            ],
                        ],
                        [
                            'key' => 'r5b',
                            'title' => 'Claustro derruido',
                            'body' => 'Columnas partidas, polvo negro. El eco del Wyrm golpea bajo la piedra.',
                            'choices' => [
                                ['text' => 'Atravesar el patio derruido', 'outcome' => 'Las losas resisten bajo tus botas.', 'difficulty' => 1, 'next' => 'r6'],
                                ['text' => 'Atajo por pasadizo oscuro', 'outcome' => 'La oscuridad aprieta, pero avanzas.', 'difficulty' => 2, 'next' => 'r6'],
                                ['text' => 'Empujar una puerta atrancada', 'outcome' => 'Cede con un golpe que duele más de la cuenta.', 'difficulty' => 3, 'next' => 'r6'],
                                ['text' => 'Esperar una calma de humo', 'outcome' => 'Respiras. Sigues.', 'difficulty' => 0, 'next' => 'r6'],
                            ],
                        ],
                    ],
                    6 => [
                        [
                            'key' => 'r6',
                            'title' => 'Anticámara del Wyrm',
                            'body' => 'El aliento del dragón llena la sala con ceniza tibia. Aquí termina el camino.',
                            'choices' => [
                                ['text' => 'Entrar con determinación', 'outcome' => 'No hay vuelta atrás.', 'difficulty' => 2],
                                ['text' => 'Ajustar el terreno para un golpe limpio', 'outcome' => 'Te expones, pero preparas el choque.', 'difficulty' => 3],
                                ['text' => 'Rodear el cráter en silencio', 'outcome' => 'Buscas el ángulo justo.', 'difficulty' => 1],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function missionSenderosDelUmbral(): array
    {
        return [
            'slug' => 'senderos-del-umbral',
            'title' => 'Senderos del Umbral',
            'intro_text' => 'En Valtherion, la palabra puede cortar más que el acero. Y el Umbral siempre escucha.',
            'context_text' => 'Debes escoger a quién creer antes de cruzar la puerta donde aguarda la Reina del Umbral.',
            'repeatable' => false,
            'base_race_points' => 25,
            'boss_slug' => 'reina-del-umbral',
            'reward' => ['xp' => 130, 'gold' => 210, 'items_json' => null],
            'graph' => [
                'steps' => [
                    1 => [
                        [
                            'key' => 'u1',
                            'title' => 'Convocatoria velada',
                            'body' => 'Un mensajero sin rostro te entrega un sello. La noche exige respuesta.',
                            'choices' => [
                                ['text' => 'Aceptar el sello y entrar por servicio', 'outcome' => 'Te cuelas sin ruido. La ciudad no te ve.', 'difficulty' => 1, 'next' => 'u2a'],
                                ['text' => 'Seguir al mensajero hasta el mercado', 'outcome' => 'Aprendes rutas, pierdes horas.', 'difficulty' => 2, 'next' => 'u2b'],
                                ['text' => 'Investigar el sello con discreción', 'outcome' => 'Obtienes pistas sin exponerte.', 'difficulty' => 0, 'next' => 'u2a'],
                                ['text' => 'Forzar la entrada principal con el sello', 'outcome' => 'Te ven. Te miden. Te marcan.', 'difficulty' => 3, 'next' => 'u2b'],
                            ],
                        ],
                    ],
                    2 => [
                        [
                            'key' => 'u2a',
                            'title' => 'Casa de Juramentos',
                            'body' => 'Velas azules iluminan rostros ocultos. Todos esperan tu postura.',
                            'choices' => [
                                ['text' => 'Aceptar una deuda temporal', 'outcome' => 'Te abren la puerta… con un hilo al cuello.', 'difficulty' => 2, 'next' => 'u3a'],
                                ['text' => 'Rechazar el pacto y exigir pruebas', 'outcome' => 'Sube la tensión. Te respetan, te vigilan.', 'difficulty' => 3, 'next' => 'u3a'],
                                ['text' => 'Observar y callar', 'outcome' => 'Aprendes nombres sin mancharte las manos.', 'difficulty' => 1, 'next' => 'u3a'],
                                ['text' => 'Ofrecer ayuda limitada', 'outcome' => 'Ganas acceso sin regalarte entero.', 'difficulty' => 0, 'next' => 'u3a'],
                            ],
                        ],
                        [
                            'key' => 'u2b',
                            'title' => 'Mercado de sombras',
                            'body' => 'Aquí se comercia con secretos. Cada palabra puede volverse contra ti.',
                            'choices' => [
                                ['text' => 'Comprar información con una reliquia', 'outcome' => 'Hablan… porque has pagado con algo vivo.', 'difficulty' => 2, 'next' => 'u3a'],
                                ['text' => 'Amenazar para abrir camino', 'outcome' => 'Te apartan. Alguien te devuelve el golpe.', 'difficulty' => 3, 'next' => 'u3a'],
                                ['text' => 'Intercambiar favores menores', 'outcome' => 'Ganas aliados discretos.', 'difficulty' => 1, 'next' => 'u3a'],
                                ['text' => 'Escuchar rumores sin intervenir', 'outcome' => 'Reúnes pistas sin exponer tu nombre.', 'difficulty' => 0, 'next' => 'u3a'],
                            ],
                        ],
                    ],
                    3 => [
                        [
                            'key' => 'u3a',
                            'title' => 'Confesor del Velo',
                            'body' => 'Un anciano pide tu versión. Su juicio abre la siguiente puerta.',
                            'choices' => [
                                ['text' => 'Decir la verdad completa', 'outcome' => 'Te concede el paso, y un silencio pesado.', 'difficulty' => 1, 'next' => 'u4a'],
                                ['text' => 'Ocultar tu objetivo', 'outcome' => 'Evitas sospechas. No evitas ojos.', 'difficulty' => 2, 'next' => 'u4b'],
                                ['text' => 'Retar su autoridad', 'outcome' => 'Te responden con dolor y te dejan pasar.', 'difficulty' => 3, 'next' => 'u4b'],
                                ['text' => 'Prometer solo lo imprescindible', 'outcome' => 'Aseguras acceso con condiciones.', 'difficulty' => 0, 'next' => 'u4a'],
                            ],
                        ],
                    ],
                    4 => [
                        [
                            'key' => 'u4a',
                            'title' => 'Cámara de votos',
                            'body' => 'El juramento está escrito en el suelo. Tu firma decide el tono del pacto.',
                            'choices' => [
                                ['text' => 'Aceptar compromiso público', 'outcome' => 'Ganas apoyo abierto. Pierdes margen.', 'difficulty' => 2, 'next' => 'u5a'],
                                ['text' => 'Firmar con reservas ocultas', 'outcome' => 'Te descubren. Te lo cobran.', 'difficulty' => 3, 'next' => 'u5a'],
                                ['text' => 'Pedir testigo neutral', 'outcome' => 'El acuerdo se vuelve más justo.', 'difficulty' => 1, 'next' => 'u5a'],
                                ['text' => 'Aceptar un pacto temporal', 'outcome' => 'Mantienes flexibilidad.', 'difficulty' => 0, 'next' => 'u5a'],
                            ],
                        ],
                        [
                            'key' => 'u4b',
                            'title' => 'Archivo prohibido',
                            'body' => 'Estanterías selladas guardan nombres que no deberían existir.',
                            'choices' => [
                                ['text' => 'Abrir el cofre sellado', 'outcome' => 'Rompes sellos… y te rompes un poco tú.', 'difficulty' => 3, 'next' => 'u5a'],
                                ['text' => 'Copiar páginas clave', 'outcome' => 'Te llevas pruebas sin destruirlo todo.', 'difficulty' => 2, 'next' => 'u5a'],
                                ['text' => 'Buscar patrones ocultos', 'outcome' => 'Entiendes lo que otros no ven.', 'difficulty' => 1, 'next' => 'u5a'],
                                ['text' => 'Cerrar el archivo y salir', 'outcome' => 'Evitas riesgos. Ignoras verdades.', 'difficulty' => 0, 'next' => 'u5a'],
                            ],
                        ],
                    ],
                    5 => [
                        [
                            'key' => 'u5a',
                            'title' => 'Cámara del pacto',
                            'body' => 'Los líderes esperan tu decisión final antes de abrir el Umbral.',
                            'choices' => [
                                ['text' => 'Ceder una concesión menor', 'outcome' => 'La firma llega sin gritos.', 'difficulty' => 1, 'next' => 'u6'],
                                ['text' => 'Exigir garantías inmediatas', 'outcome' => 'Aprietas. Te aprietan.', 'difficulty' => 2, 'next' => 'u6'],
                                ['text' => 'Romper el silencio con un desafío', 'outcome' => 'Te lo devuelven en carne.', 'difficulty' => 3, 'next' => 'u6'],
                            ],
                        ],
                    ],
                    6 => [
                        [
                            'key' => 'u6',
                            'title' => 'Puerta del Umbral',
                            'body' => 'El Umbral vibra. Al otro lado, la Reina espera con su propia verdad.',
                            'choices' => [
                                ['text' => 'Cruzar con firmeza', 'outcome' => 'El juicio final comienza.', 'difficulty' => 2],
                                ['text' => 'Entrar con un pacto preparado', 'outcome' => 'Intentas ganar tiempo antes del duelo.', 'difficulty' => 3],
                                ['text' => 'Tomar el paso lateral', 'outcome' => 'Buscas ventaja sin hacer ruido.', 'difficulty' => 1],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function missionSalDelDestino(): array
    {
        return [
            'slug' => 'sal-del-destino',
            'title' => 'Sal del Destino',
            'intro_text' => 'La sal no perdona. Borra huellas, raja piel y deja promesas rotas a cielo abierto.',
            'context_text' => 'Debes cruzar las salinas antes de que despierte el Titano. El Juramento se paga con resistencia.',
            'repeatable' => false,
            'base_race_points' => 30,
            'boss_slug' => 'titano-de-sal',
            'reward' => ['xp' => 150, 'gold' => 260, 'items_json' => null],
            'graph' => [
                'steps' => [
                    1 => [
                        [
                            'key' => 's1',
                            'title' => 'Dunas cortantes',
                            'body' => 'El viento levanta cuchillas de sal. Cada paso deja una herida fina.',
                            'choices' => [
                                ['text' => 'Cruzar a campo abierto', 'outcome' => 'Avanzas rápido. El viento te castiga.', 'difficulty' => 2, 'next' => 's2a'],
                                ['text' => 'Seguir restos de caravana', 'outcome' => 'Las huellas guían entre crestas.', 'difficulty' => 1, 'next' => 's2b'],
                                ['text' => 'Tomar un desvío largo', 'outcome' => 'Pierdes tiempo, evitas lo peor.', 'difficulty' => 0, 'next' => 's2b'],
                                ['text' => 'Atravesar un cañón estrecho', 'outcome' => 'La ruta muerde. Sales marcado.', 'difficulty' => 3, 'next' => 's2a'],
                            ],
                        ],
                    ],
                    2 => [
                        [
                            'key' => 's2a',
                            'title' => 'Cañón de sal',
                            'body' => 'Las paredes reflejan la luz como espejos. El eco confunde la dirección.',
                            'choices' => [
                                ['text' => 'Escalar por vetas claras', 'outcome' => 'Agota, pero te orienta.', 'difficulty' => 2, 'next' => 's3a'],
                                ['text' => 'Seguir el cauce seco', 'outcome' => 'La pendiente es amable. Ahorras fuerzas.', 'difficulty' => 1, 'next' => 's3b'],
                                ['text' => 'Atajo por grietas', 'outcome' => 'Cristales afilados. Pagas con piel.', 'difficulty' => 3, 'next' => 's3a'],
                                ['text' => 'Esperar a que pase la ventisca', 'outcome' => 'Ganas claridad. Pierdes horas.', 'difficulty' => 0, 'next' => 's3b'],
                            ],
                        ],
                        [
                            'key' => 's2b',
                            'title' => 'Laguna blanca',
                            'body' => 'La superficie se quiebra bajo tus botas. El agua salobre atrae peligros.',
                            'choices' => [
                                ['text' => 'Bordear la laguna', 'outcome' => 'Evitas hundirte en barro salino.', 'difficulty' => 1, 'next' => 's3b'],
                                ['text' => 'Cruzar en línea recta', 'outcome' => 'Resbalas, te golpeas. Sigues.', 'difficulty' => 3, 'next' => 's3a'],
                                ['text' => 'Buscar pasarelas de roca', 'outcome' => 'Encuentras un camino firme.', 'difficulty' => 2, 'next' => 's3b'],
                                ['text' => 'Esperar la marea baja', 'outcome' => 'La ruta se expone. Avanzas sin prisa.', 'difficulty' => 0, 'next' => 's3a'],
                            ],
                        ],
                    ],
                    3 => [
                        [
                            'key' => 's3a',
                            'title' => 'Caravana rota',
                            'body' => 'Carros destruidos y huellas frescas. Alguien pasó hace poco.',
                            'choices' => [
                                ['text' => 'Revisar restos en busca de agua', 'outcome' => 'Encuentras un cántaro medio lleno.', 'difficulty' => 1, 'next' => 's4a'],
                                ['text' => 'Perseguir huellas sin pausa', 'outcome' => 'Aceleras. El sol te cobra.', 'difficulty' => 3, 'next' => 's4a'],
                                ['text' => 'Usar los carros como cobertura', 'outcome' => 'Descansas un minuto, no una vida.', 'difficulty' => 0, 'next' => 's4a'],
                                ['text' => 'Improvisar un trineo de sal', 'outcome' => 'Ahorras pasos, gastas músculo.', 'difficulty' => 2, 'next' => 's4a'],
                            ],
                        ],
                        [
                            'key' => 's3b',
                            'title' => 'Puesto abandonado',
                            'body' => 'Una torre vacía. Señales de ataque reciente. Nadie murió gratis.',
                            'choices' => [
                                ['text' => 'Subir y observar', 'outcome' => 'Ubicas rutas antes de bajar.', 'difficulty' => 1, 'next' => 's4a'],
                                ['text' => 'Revisar reservas ocultas', 'outcome' => 'Encuentras provisiones, la estructura cede.', 'difficulty' => 3, 'next' => 's4a'],
                                ['text' => 'Encender una señal breve', 'outcome' => 'Nadie responde. Te da calma.', 'difficulty' => 0, 'next' => 's4a'],
                                ['text' => 'Reforzar equipo con restos', 'outcome' => 'Aguantas mejor lo que viene.', 'difficulty' => 2, 'next' => 's4a'],
                            ],
                        ],
                    ],
                    4 => [
                        [
                            'key' => 's4a',
                            'title' => 'Tormenta de cristales',
                            'body' => 'El viento se vuelve cuchilla. La sal golpea como granizo.',
                            'choices' => [
                                ['text' => 'Cubrirte y avanzar', 'outcome' => 'Resistes. Terminas herido.', 'difficulty' => 3, 'next' => 's5a'],
                                ['text' => 'Refugiarte bajo una cornisa', 'outcome' => 'Esperas. El desierto se ríe.', 'difficulty' => 0, 'next' => 's5b'],
                                ['text' => 'Seguir el viento a favor', 'outcome' => 'Aprovechas dirección, pierdes control.', 'difficulty' => 2, 'next' => 's5a'],
                                ['text' => 'Marcar camino con estacas', 'outcome' => 'Aseguras ruta, gastas tiempo.', 'difficulty' => 1, 'next' => 's5b'],
                            ],
                        ],
                    ],
                    5 => [
                        [
                            'key' => 's5a',
                            'title' => 'Círculo de menhires',
                            'body' => 'Piedras antiguas forman un anillo. La vibración del Titano crece.',
                            'choices' => [
                                ['text' => 'Cruzar entre menhires', 'outcome' => 'Sientes energía extraña. Sigues.', 'difficulty' => 2, 'next' => 's6'],
                                ['text' => 'Rodeo largo', 'outcome' => 'Evitas riesgo directo.', 'difficulty' => 0, 'next' => 's6'],
                                ['text' => 'Escalar la piedra central', 'outcome' => 'El esfuerzo te pasa factura.', 'difficulty' => 3, 'next' => 's6'],
                            ],
                        ],
                        [
                            'key' => 's5b',
                            'title' => 'Risco del silbido',
                            'body' => 'El viento suena como advertencia. La arena se mueve sola.',
                            'choices' => [
                                ['text' => 'Atravesar la ladera en diagonal', 'outcome' => 'Duro, pero estable.', 'difficulty' => 2, 'next' => 's6'],
                                ['text' => 'Descender por canal rocoso', 'outcome' => 'El canal te guía al valle.', 'difficulty' => 1, 'next' => 's6'],
                                ['text' => 'Cruzar por la arista expuesta', 'outcome' => 'El viento te hiere sin piedad.', 'difficulty' => 3, 'next' => 's6'],
                                ['text' => 'Esperar el cambio de viento', 'outcome' => 'Aprovechas una pausa segura.', 'difficulty' => 0, 'next' => 's6'],
                            ],
                        ],
                    ],
                    6 => [
                        [
                            'key' => 's6',
                            'title' => 'Atrio del Titano',
                            'body' => 'La sal vibra bajo tus pies. El Titano despierta en un silencio absoluto.',
                            'choices' => [
                                ['text' => 'Afrontar el duelo de frente', 'outcome' => 'La batalla final comienza.', 'difficulty' => 2],
                                ['text' => 'Buscar un punto débil antes de entrar', 'outcome' => 'Te expones para preparar el golpe.', 'difficulty' => 3],
                                ['text' => 'Rodear la plataforma con cautela', 'outcome' => 'Intentas ganar posición.', 'difficulty' => 1],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function genericMissionForBoss(FinalBoss $boss, string $templateSlug): array
    {
        $bossName = trim($boss->name) !== '' ? $boss->name : Str::headline($boss->slug);

        $slug = 'juramento-' . $boss->slug;
        $title = 'El Juramento: ' . $bossName;

        $intro = 'Hay caminos que no llevan a un lugar, sino a una deuda. Valtherion no perdona a quien duda.';
        $context = 'Seis tramos para llegar al nombre escrito al final: ' . $bossName . '. Si el Juramento te quiere vivo, lo sabrás tarde.';

        // Plantillas de ambiente (sin romper el lore del libro; sirve para reemplazar luego).
        if ($templateSlug === 'catedral-del-juramento') {
            $intro = 'La piedra canta rezos antiguos. Aquí, los votos no se pronuncian: se graban en hueso.';
            $context = 'Buscas la nave sellada bajo la catedral. Al fondo te espera ' . $bossName . ', guardián de un pacto impuro.';
        } elseif ($templateSlug === 'bosque-de-la-escarcha-negra') {
            $intro = 'El hielo no enfría: detiene. El bosque conserva voces en la escarcha como insectos en ámbar.';
            $context = 'Sigues una senda de marcas negras. Si te equivocas, el bosque te traga. Al final: ' . $bossName . '.';
        } elseif ($templateSlug === 'foso-de-hierro-y-sangre') {
            $intro = 'El hierro sueña con guerra. El foso huele a sangre vieja y promesas rotas.';
            $context = 'Desciendes por corredores sin sol. Cada decisión pesa. Al final, el nombre que nadie pronuncia: ' . $bossName . '.';
        }

        $p = substr(Str::slug($boss->slug, ''), 0, 6);
        if ($p === '') {
            $p = 'jb';
        }

        return [
            'slug' => $slug,
            'title' => $title,
            'intro_text' => $intro,
            'context_text' => $context,
            'repeatable' => false,
            'base_race_points' => 20,
            'boss_slug' => $boss->slug,
            'reward' => ['xp' => 120, 'gold' => 0, 'items_json' => null],
            'graph' => [
                'steps' => [
                    1 => [
                        [
                            'key' => "{$p}1",
                            'title' => 'Primer umbral',
                            'body' => 'El aire cambia. No es viento: es una presencia que te mide. Un juramento viejo se te pega a la lengua.',
                            'choices' => [
                                ['text' => 'Cruzar sin detenerte', 'outcome' => 'Avanzas rápido, pero algo te sigue la respiración.', 'difficulty' => 2, 'next' => "{$p}2a"],
                                ['text' => 'Examinar señales del suelo', 'outcome' => 'Lees el camino. Pierdes tiempo, ganas control.', 'difficulty' => 0, 'next' => "{$p}2b"],
                                ['text' => 'Forzar un atajo por terreno roto', 'outcome' => 'Te abre paso… y te deja marca.', 'difficulty' => 3, 'next' => "{$p}2a"],
                                ['text' => 'Ir pegado a los muros', 'outcome' => 'Más lento, más seguro. Por ahora.', 'difficulty' => 1, 'next' => "{$p}2b"],
                            ],
                        ],
                    ],
                    2 => [
                        [
                            'key' => "{$p}2a",
                            'title' => 'Pasaje de sombra',
                            'body' => 'El corredor se estrecha. Las paredes están rayadas como si alguien hubiera querido salir de sí mismo.',
                            'choices' => [
                                ['text' => 'Avanzar a oscuras sin encender nada', 'outcome' => 'No te ven… hasta que te rozan.', 'difficulty' => 2, 'next' => "{$p}3a"],
                                ['text' => 'Encender una luz breve', 'outcome' => 'Ves la ruta, no ves lo que mira desde fuera.', 'difficulty' => 1, 'next' => "{$p}3b"],
                                ['text' => 'Correr para romper el miedo', 'outcome' => 'Rompes el miedo. Te rompe el suelo.', 'difficulty' => 3, 'next' => "{$p}3a"],
                                ['text' => 'Esperar y escuchar', 'outcome' => 'Escuchas el ritmo del lugar. Te acomodas a él.', 'difficulty' => 0, 'next' => "{$p}3b"],
                            ],
                        ],
                        [
                            'key' => "{$p}2b",
                            'title' => 'Sendero marcado',
                            'body' => 'Encuentras marcas antiguas: no son guías… son avisos. Alguien lo intentó antes.',
                            'choices' => [
                                ['text' => 'Seguir las marcas al pie de la letra', 'outcome' => 'El camino se abre con menos resistencia.', 'difficulty' => 1, 'next' => "{$p}3b"],
                                ['text' => 'Interpretarlas y tomar tu propia línea', 'outcome' => 'Ganas distancia, arriesgas verdad.', 'difficulty' => 2, 'next' => "{$p}3a"],
                                ['text' => 'Ignorarlas y entrar por la zona rota', 'outcome' => 'Te tragas polvo y dolor. Sigues.', 'difficulty' => 3, 'next' => "{$p}3a"],
                                ['text' => 'Revisar el perímetro primero', 'outcome' => 'Evitas un error fácil. No evitas el destino.', 'difficulty' => 0, 'next' => "{$p}3b"],
                            ],
                        ],
                    ],
                    3 => [
                        [
                            'key' => "{$p}3a",
                            'title' => 'Punto de ruptura',
                            'body' => 'Todo se vuelve más silencioso. Hasta tus pensamientos suenan demasiado alto.',
                            'choices' => [
                                ['text' => 'Empujar una puerta atrancada', 'outcome' => 'Cede con un golpe que duele.', 'difficulty' => 3, 'next' => "{$p}4a"],
                                ['text' => 'Buscar una salida lateral', 'outcome' => 'Encuentras un hueco estable.', 'difficulty' => 1, 'next' => "{$p}4b"],
                                ['text' => 'Avanzar por el centro, sin dudar', 'outcome' => 'No dudas. Tampoco el lugar.', 'difficulty' => 2, 'next' => "{$p}4a"],
                                ['text' => 'Reordenar equipo y respirar', 'outcome' => 'La calma te compra precisión.', 'difficulty' => 0, 'next' => "{$p}4b"],
                            ],
                        ],
                        [
                            'key' => "{$p}3b",
                            'title' => 'Sala de ecos',
                            'body' => 'El sonido rebota como si hubiera otro tú caminando al lado. No miras. Sigues.',
                            'choices' => [
                                ['text' => 'Responder al eco con una señal', 'outcome' => 'Algo responde. No era tu eco.', 'difficulty' => 2, 'next' => "{$p}4b"],
                                ['text' => 'Mantener silencio absoluto', 'outcome' => 'El lugar se calma. Tú también.', 'difficulty' => 1, 'next' => "{$p}4a"],
                                ['text' => 'Romper la sala a golpes para callarla', 'outcome' => 'Calla. Te deja herida.', 'difficulty' => 3, 'next' => "{$p}4b"],
                                ['text' => 'Esperar a que el eco se apague', 'outcome' => 'Pierdes tiempo. Ganas estabilidad.', 'difficulty' => 0, 'next' => "{$p}4a"],
                            ],
                        ],
                    ],
                    4 => [
                        [
                            'key' => "{$p}4a",
                            'title' => 'Tramo del precio',
                            'body' => 'El camino exige tributo: fuerza, sangre o tiempo. El Juramento elige contigo.',
                            'choices' => [
                                ['text' => 'Forzar el paso por el terreno hostil', 'outcome' => 'Te abre paso… y te deja marca.', 'difficulty' => 3, 'next' => "{$p}5"],
                                ['text' => 'Elegir la ruta larga pero firme', 'outcome' => 'Avanzas sin sobresaltos.', 'difficulty' => 0, 'next' => "{$p}5"],
                                ['text' => 'Aprovechar un atajo estrecho', 'outcome' => 'Ganas terreno con un riesgo controlado.', 'difficulty' => 2, 'next' => "{$p}5"],
                                ['text' => 'Buscar cobertura y moverte por sombras', 'outcome' => 'Llegas entero, más despacio.', 'difficulty' => 1, 'next' => "{$p}5"],
                            ],
                        ],
                        [
                            'key' => "{$p}4b",
                            'title' => 'Ruta del filo',
                            'body' => 'Un pasillo donde todo roza. Piedra, hierro… y tu paciencia.',
                            'choices' => [
                                ['text' => 'Avanzar pegado al borde', 'outcome' => 'Te mantienes estable. No cómodo.', 'difficulty' => 1, 'next' => "{$p}5"],
                                ['text' => 'Correr para salir de una vez', 'outcome' => 'Sales rápido. Sales herido.', 'difficulty' => 3, 'next' => "{$p}5"],
                                ['text' => 'Medir cada paso antes de darlo', 'outcome' => 'No fallas. Tampoco brillas.', 'difficulty' => 0, 'next' => "{$p}5"],
                                ['text' => 'Cruzar por el centro con decisión', 'outcome' => 'Te expones para ganar distancia.', 'difficulty' => 2, 'next' => "{$p}5"],
                            ],
                        ],
                    ],
                    5 => [
                        [
                            'key' => "{$p}5",
                            'title' => 'Convergencia',
                            'body' => 'Las rutas se juntan. El aire pesa distinto. Estás cerca del nombre final.',
                            'choices' => [
                                ['text' => 'Seguir el rastro más directo', 'outcome' => 'Te acercas rápido. No gratis.', 'difficulty' => 2, 'next' => "{$p}6"],
                                ['text' => 'Revisar una última vez tus armas', 'outcome' => 'Llegas más preparado.', 'difficulty' => 1, 'next' => "{$p}6"],
                                ['text' => 'Forzar un acceso sellado', 'outcome' => 'Lo abres… pagando en carne.', 'difficulty' => 3, 'next' => "{$p}6"],
                                ['text' => 'Descansar solo un minuto', 'outcome' => 'Recuperas el pulso.', 'difficulty' => 0, 'next' => "{$p}6"],
                            ],
                        ],
                    ],
                    6 => [
                        [
                            'key' => "{$p}6",
                            'title' => 'Antesala',
                            'body' => 'El último tramo. El lugar parece contener la respiración. Al otro lado te espera ' . $bossName . '.',
                            'choices' => [
                                ['text' => 'Entrar con el acero listo', 'outcome' => 'El choque es inevitable.', 'difficulty' => 2],
                                ['text' => 'Preparar el terreno con riesgo', 'outcome' => 'Ganas ventaja… si sobrevives a prepararla.', 'difficulty' => 3],
                                ['text' => 'Buscar un ángulo favorable', 'outcome' => 'No es cobardía: es guerra.', 'difficulty' => 1],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
