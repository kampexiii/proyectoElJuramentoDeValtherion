<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FinalBoss;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Models\MissionReward;
use App\Services\Missions\MissionGraphValidator;
use Illuminate\Database\Seeder;
use RuntimeException;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        $missions = $this->missionDefinitions();
        $validator = app(MissionGraphValidator::class);

        foreach ($missions as $data) {
            $boss = FinalBoss::query()->where('slug', $data['boss_slug'])->first();
            if (!$boss) {
                throw new RuntimeException('Boss no encontrado para la mision: ' . $data['slug']);
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

            $this->seedGraphForMission($mission, $data['graph']);

            $mission->refresh()->load('reward');
            $errors = $validator->validate($mission);
            if (!empty($errors)) {
                throw new RuntimeException('Mision invalida (' . $mission->slug . '): ' . implode(' | ', $errors));
            }
        }
    }

    private function seedGraphForMission(Mission $mission, array $graph): void
    {
        $existingNodeIds = $mission->nodes()->pluck('id');
        if ($existingNodeIds->isNotEmpty()) {
            MissionChoice::query()->whereIn('mission_node_id', $existingNodeIds)->delete();
        }
        MissionNode::query()->where('mission_id', $mission->id)->delete();

        $stepNodes = [];

        foreach ($graph['steps'] as $step => $nodes) {
            foreach ($nodes as $index => $nodeData) {
                $node = MissionNode::query()->create([
                    'mission_id' => $mission->id,
                    'step_index' => $step,
                    'is_start' => $step === 1 && $index === 0,
                    'title' => $nodeData['title'],
                    'body_text' => $nodeData['body'],
                ]);
                $stepNodes[$step][$nodeData['key']] = $node;
            }
        }

        foreach ($graph['steps'] as $step => $nodes) {
            foreach ($nodes as $nodeData) {
                $node = $stepNodes[$step][$nodeData['key']];

                if ($step < 6) {
                    $this->createBranchChoices($node, $nodeData['choices'], $stepNodes[$step + 1]);
                } else {
                    $this->createBossChoices($node, $nodeData['choices']);
                }
            }
        }
    }

    private function createBranchChoices(MissionNode $node, array $choices, array $nextNodes): void
    {
        $orders = range(1, count($choices));
        shuffle($orders);

        foreach ($choices as $index => $choice) {
            $nextKey = $choice['next'];
            $nextNode = $nextNodes[$nextKey] ?? null;
            if (!$nextNode) {
                throw new RuntimeException('Nodo siguiente no encontrado para el nodo #' . $node->id . '.');
            }

            $difficulty = (int) $choice['difficulty'];
            $effects = $difficulty === 3 ? ['wounds' => 1] : null;

            MissionChoice::query()->create([
                'mission_node_id' => $node->id,
                'choice_text' => $choice['text'],
                'outcome_text' => $choice['outcome'],
                'difficulty_points' => $difficulty,
                'effects_json' => $effects,
                'next_node_id' => $nextNode->id,
                'goes_to_boss' => false,
                'order' => $orders[$index],
            ]);
        }
    }

    private function createBossChoices(MissionNode $node, array $choices): void
    {
        $orders = range(1, count($choices));
        shuffle($orders);

        foreach ($choices as $index => $choice) {
            $difficulty = (int) $choice['difficulty'];
            $effects = $difficulty === 3 ? ['wounds' => 1] : null;

            MissionChoice::query()->create([
                'mission_node_id' => $node->id,
                'choice_text' => $choice['text'],
                'outcome_text' => $choice['outcome'],
                'difficulty_points' => $difficulty,
                'effects_json' => $effects,
                'next_node_id' => null,
                'goes_to_boss' => true,
                'order' => $orders[$index],
            ]);
        }
    }

    private function missionDefinitions(): array
    {
        return [
            [
                'slug' => 'ruinas-de-ceniza',
                'title' => 'Ruinas de Ceniza',
                'intro_text' => 'Las ruinas respiran humo frio y metal oxidado. Nada queda intacto.',
                'context_text' => 'Exploras una ciudad enterrada buscando el paso hacia el Wyrm.',
                'repeatable' => true,
                'base_race_points' => 20,
                'boss_slug' => 'wyrm-de-ceniza',
                'reward' => ['xp' => 110, 'gold' => 180, 'items_json' => null],
                'graph' => [
                    'steps' => [
                        1 => [
                            [
                                'key' => 'r1',
                                'title' => 'Portico hundido',
                                'body' => 'El portico se parte bajo el peso de la ceniza. El aire quema la garganta.',
                                'choices' => [
                                    ['text' => 'Deslizarte por la grieta estrecha', 'outcome' => 'Ganas altura pero el metal corta la piel.', 'difficulty' => 2, 'next' => 'r2a'],
                                    ['text' => 'Forzar la puerta oxidada', 'outcome' => 'El hierro cede con un chillido y te lastimas.', 'difficulty' => 3, 'next' => 'r2b'],
                                    ['text' => 'Rodear por las arcadas caidas', 'outcome' => 'El camino es mas largo pero estable.', 'difficulty' => 1, 'next' => 'r2a'],
                                    ['text' => 'Esperar a que baje el humo', 'outcome' => 'Pierdes tiempo pero avanzas sin sobresaltos.', 'difficulty' => 0, 'next' => 'r2b'],
                                ],
                            ],
                        ],
                        2 => [
                            [
                                'key' => 'r2a',
                                'title' => 'Galeria de vidrio',
                                'body' => 'Los techos cuelgan en silencio y cada paso resuena como un eco de incendio.',
                                'choices' => [
                                    ['text' => 'Cruzar por vigas de cristal', 'outcome' => 'El suelo vibra, pero la vista te guia.', 'difficulty' => 2, 'next' => 'r3a'],
                                    ['text' => 'Bajar por el pozo de ventilacion', 'outcome' => 'El descenso es brusco y te golpeas.', 'difficulty' => 3, 'next' => 'r3b'],
                                    ['text' => 'Seguir las marcas de rescate', 'outcome' => 'Encuentras una ruta marcada por antiguos guardias.', 'difficulty' => 1, 'next' => 'r3a'],
                                    ['text' => 'Ahorrar fuerzas y avanzar lento', 'outcome' => 'La marcha es tediosa pero segura.', 'difficulty' => 0, 'next' => 'r3b'],
                                ],
                            ],
                            [
                                'key' => 'r2b',
                                'title' => 'Taller sepultado',
                                'body' => 'Herramientas calcinadas sobresalen del polvo. Algo se mueve bajo los escombros.',
                                'choices' => [
                                    ['text' => 'Buscar herramientas utiles', 'outcome' => 'Recuperas equipo, pero pierdes tiempo.', 'difficulty' => 1, 'next' => 'r3b'],
                                    ['text' => 'Acelerar por la zona inestable', 'outcome' => 'El techo cae y te lastimas al escapar.', 'difficulty' => 3, 'next' => 'r3a'],
                                    ['text' => 'Usar un camino lateral estrecho', 'outcome' => 'El atajo evita las vigas mas pesadas.', 'difficulty' => 2, 'next' => 'r3b'],
                                    ['text' => 'Apagar brasas con arena', 'outcome' => 'Enfrias el suelo y avanzas firme.', 'difficulty' => 0, 'next' => 'r3a'],
                                ],
                            ],
                        ],
                        3 => [
                            [
                                'key' => 'r3a',
                                'title' => 'Anfiteatro rojo',
                                'body' => 'El escenario esta fundido en un rojo oscuro. El silencio pesa mas que el humo.',
                                'choices' => [
                                    ['text' => 'Subir las gradas quemadas', 'outcome' => 'Ganas una salida alta con vista amplia.', 'difficulty' => 1, 'next' => 'r4a'],
                                    ['text' => 'Explorar el palco derruido', 'outcome' => 'Encuentras un pasaje oculto entre escombros.', 'difficulty' => 2, 'next' => 'r4a'],
                                    ['text' => 'Cortar por el foso central', 'outcome' => 'El calor te desgasta, pero avanzas rapido.', 'difficulty' => 3, 'next' => 'r4a'],
                                    ['text' => 'Tomar un respiro y ordenar equipo', 'outcome' => 'Recuperas el aliento antes de seguir.', 'difficulty' => 0, 'next' => 'r4a'],
                                ],
                            ],
                            [
                                'key' => 'r3b',
                                'title' => 'Camara de ceniza',
                                'body' => 'Una sala circular con ceniza hasta los tobillos y señales borrosas en las paredes.',
                                'choices' => [
                                    ['text' => 'Seguir el humo frio', 'outcome' => 'La corriente marca el rumbo con claridad.', 'difficulty' => 2, 'next' => 'r4a'],
                                    ['text' => 'Forzar la compuerta sellada', 'outcome' => 'La puerta cede, pero te hiere el metal.', 'difficulty' => 3, 'next' => 'r4a'],
                                    ['text' => 'Examinar simbolos de evacuacion', 'outcome' => 'Los simbolos indican una salida estable.', 'difficulty' => 1, 'next' => 'r4a'],
                                    ['text' => 'Esperar a que se asiente el polvo', 'outcome' => 'La visibilidad mejora tras unos minutos.', 'difficulty' => 0, 'next' => 'r4a'],
                                ],
                            ],
                        ],
                        4 => [
                            [
                                'key' => 'r4a',
                                'title' => 'Biblioteca quemada',
                                'body' => 'Estanterias calcinadas y manuscritos pegados a la piedra. Al fondo, dos rutas.',
                                'choices' => [
                                    ['text' => 'Escaleras rotas hacia el foso', 'outcome' => 'Bajas con cuidado entre tablones inestables.', 'difficulty' => 2, 'next' => 'r5a'],
                                    ['text' => 'Pasadizo de monjes', 'outcome' => 'El corredor es estrecho pero seguro.', 'difficulty' => 1, 'next' => 'r5b'],
                                    ['text' => 'Atravesar la sala de brasas', 'outcome' => 'El calor muerde y te deja marcas.', 'difficulty' => 3, 'next' => 'r5a'],
                                    ['text' => 'Ruta lenta por el claustro exterior', 'outcome' => 'El rodeo evita el fuego directo.', 'difficulty' => 0, 'next' => 'r5b'],
                                ],
                            ],
                        ],
                        5 => [
                            [
                                'key' => 'r5a',
                                'title' => 'Foso de brasas',
                                'body' => 'El suelo vibra y el aire late. El Wyrm duerme cerca.',
                                'choices' => [
                                    ['text' => 'Descender por cadenas oxidadas', 'outcome' => 'El metal aguanta con esfuerzo.', 'difficulty' => 2, 'next' => 'r6'],
                                    ['text' => 'Saltar entre pilares calientes', 'outcome' => 'El riesgo es alto, pero avanzas rapido.', 'difficulty' => 3, 'next' => 'r6'],
                                    ['text' => 'Buscar un puente antiguo', 'outcome' => 'Encuentras un paso mas seguro.', 'difficulty' => 1, 'next' => 'r6'],
                                ],
                            ],
                            [
                                'key' => 'r5b',
                                'title' => 'Claustro derruido',
                                'body' => 'Columnas partidas y polvo negro. El eco del Wyrm retumba bajo la piedra.',
                                'choices' => [
                                    ['text' => 'Atravesar el patio derruido', 'outcome' => 'Las losas resisten bajo tus botas.', 'difficulty' => 1, 'next' => 'r6'],
                                    ['text' => 'Atajo por pasadizo oscuro', 'outcome' => 'La oscuridad es densa, pero avanzas rapido.', 'difficulty' => 2, 'next' => 'r6'],
                                    ['text' => 'Empujar la puerta atrancada', 'outcome' => 'La madera cede con un golpe doloroso.', 'difficulty' => 3, 'next' => 'r6'],
                                    ['text' => 'Esperar una calma de humo', 'outcome' => 'El aire se vuelve respirable por un momento.', 'difficulty' => 0, 'next' => 'r6'],
                                ],
                            ],
                        ],
                        6 => [
                            [
                                'key' => 'r6',
                                'title' => 'Antecamera del Wyrm',
                                'body' => 'El suelo palpita y el aliento del dragon llena la sala con ceniza tibia.',
                                'choices' => [
                                    ['text' => 'Entrar con determinacion', 'outcome' => 'No hay vuelta atras.', 'difficulty' => 2],
                                    ['text' => 'Preparar una trampa rapida', 'outcome' => 'Ajustas el terreno antes del choque.', 'difficulty' => 3],
                                    ['text' => 'Rodear el crater en silencio', 'outcome' => 'Buscas un angulo favorable.', 'difficulty' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'senderos-del-umbral',
                'title' => 'Senderos del Umbral',
                'intro_text' => 'La ciudad susurra pactos en voz baja. Cada gesto pesa mas que una espada.',
                'context_text' => 'Debes decidir a quien creer antes de enfrentar a la Reina del Umbral.',
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
                                'body' => 'Un mensajero sin rostro te entrega un sello. La noche exige una respuesta.',
                                'choices' => [
                                    ['text' => 'Aceptar el sello y entrar por la puerta de servicio', 'outcome' => 'Te infiltras sin llamar la atencion.', 'difficulty' => 1, 'next' => 'u2a'],
                                    ['text' => 'Seguir al mensajero hasta el mercado', 'outcome' => 'Aprendes sus rutas, pero pierdes tiempo.', 'difficulty' => 2, 'next' => 'u2b'],
                                    ['text' => 'Investigar el sello con los vecinos', 'outcome' => 'Obtienes pistas discretas antes de seguir.', 'difficulty' => 0, 'next' => 'u2a'],
                                    ['text' => 'Forzar la entrada principal con el sello', 'outcome' => 'Te expones a los guardianes del pacto.', 'difficulty' => 3, 'next' => 'u2b'],
                                ],
                            ],
                        ],
                        2 => [
                            [
                                'key' => 'u2a',
                                'title' => 'Casa de juramentos',
                                'body' => 'Velas azules iluminan rostros ocultos. Todos esperan tu postura.',
                                'choices' => [
                                    ['text' => 'Aceptar una deuda temporal', 'outcome' => 'Ganas acceso, pero quedas comprometido.', 'difficulty' => 2, 'next' => 'u3a'],
                                    ['text' => 'Rechazar el pacto y exigir pruebas', 'outcome' => 'La tension sube, pero te respetan.', 'difficulty' => 3, 'next' => 'u3a'],
                                    ['text' => 'Escuchar en silencio y observar', 'outcome' => 'Descubres nombres clave sin intervenir.', 'difficulty' => 1, 'next' => 'u3a'],
                                    ['text' => 'Ofrecer ayuda limitada', 'outcome' => 'Mantienes margen sin cerrar puertas.', 'difficulty' => 0, 'next' => 'u3a'],
                                ],
                            ],
                            [
                                'key' => 'u2b',
                                'title' => 'Mercado de sombras',
                                'body' => 'El trueque se hace con secretos. Cada palabra puede volverse contra ti.',
                                'choices' => [
                                    ['text' => 'Comprar informacion con una reliquia', 'outcome' => 'Los mercaderes hablan, pero pagas caro.', 'difficulty' => 2, 'next' => 'u3a'],
                                    ['text' => 'Amenazar para abrir camino', 'outcome' => 'La multitud se aparta y alguien te hiere.', 'difficulty' => 3, 'next' => 'u3a'],
                                    ['text' => 'Intercambiar favores menores', 'outcome' => 'Ganas aliados discretos.', 'difficulty' => 1, 'next' => 'u3a'],
                                    ['text' => 'Escuchar rumores sin intervenir', 'outcome' => 'Reunes pistas sin exponerte.', 'difficulty' => 0, 'next' => 'u3a'],
                                ],
                            ],
                        ],
                        3 => [
                            [
                                'key' => 'u3a',
                                'title' => 'Confesor del Velo',
                                'body' => 'Un anciano pide tu version de los hechos. Su juicio abre la siguiente puerta.',
                                'choices' => [
                                    ['text' => 'Decir la verdad completa', 'outcome' => 'El confesor te concede paso directo.', 'difficulty' => 1, 'next' => 'u4a'],
                                    ['text' => 'Ocultar tu objetivo real', 'outcome' => 'Evitas sospechas, pero te observan.', 'difficulty' => 2, 'next' => 'u4b'],
                                    ['text' => 'Retar su autoridad', 'outcome' => 'La tension escala y sales con heridas.', 'difficulty' => 3, 'next' => 'u4b'],
                                    ['text' => 'Hacer una promesa limitada', 'outcome' => 'Aseguras acceso con condiciones.', 'difficulty' => 0, 'next' => 'u4a'],
                                ],
                            ],
                        ],
                        4 => [
                            [
                                'key' => 'u4a',
                                'title' => 'Camara de votos',
                                'body' => 'El juramento esta escrito en el suelo. Tu firma decide el tono del pacto.',
                                'choices' => [
                                    ['text' => 'Aceptar un compromiso publico', 'outcome' => 'Ganas apoyo abierto, pero menos margen.', 'difficulty' => 2, 'next' => 'u5a'],
                                    ['text' => 'Firmar con reservas secretas', 'outcome' => 'El truco se nota y te golpean.', 'difficulty' => 3, 'next' => 'u5a'],
                                    ['text' => 'Pedir un testigo neutral', 'outcome' => 'El acuerdo se vuelve mas justo.', 'difficulty' => 1, 'next' => 'u5a'],
                                    ['text' => 'Aceptar un pacto temporal', 'outcome' => 'Mantienes flexibilidad.', 'difficulty' => 0, 'next' => 'u5a'],
                                ],
                            ],
                            [
                                'key' => 'u4b',
                                'title' => 'Archivo prohibido',
                                'body' => 'Estanterias selladas guardan nombres que no deberias leer.',
                                'choices' => [
                                    ['text' => 'Abrir el cofre sellado', 'outcome' => 'Los sellos se rompen y te lastimas.', 'difficulty' => 3, 'next' => 'u5a'],
                                    ['text' => 'Copiar paginas clave', 'outcome' => 'Reunes pruebas sin destruir todo.', 'difficulty' => 2, 'next' => 'u5a'],
                                    ['text' => 'Buscar un atajo de lectura', 'outcome' => 'Encuentras patrones ocultos.', 'difficulty' => 1, 'next' => 'u5a'],
                                    ['text' => 'Cerrar el archivo y salir', 'outcome' => 'Evitas riesgos, pero sabes menos.', 'difficulty' => 0, 'next' => 'u5a'],
                                ],
                            ],
                        ],
                        5 => [
                            [
                                'key' => 'u5a',
                                'title' => 'Camara del pacto',
                                'body' => 'Los lideres esperan tu decision final antes de abrir el Umbral.',
                                'choices' => [
                                    ['text' => 'Ceder una concesion menor', 'outcome' => 'El acuerdo se firma con calma.', 'difficulty' => 1, 'next' => 'u6'],
                                    ['text' => 'Exigir garantias inmediatas', 'outcome' => 'El ambiente se tensa, pero avanzas.', 'difficulty' => 2, 'next' => 'u6'],
                                    ['text' => 'Romper el silencio con un desafio', 'outcome' => 'El choque te deja marcas.', 'difficulty' => 3, 'next' => 'u6'],
                                ],
                            ],
                        ],
                        6 => [
                            [
                                'key' => 'u6',
                                'title' => 'Puerta del Umbral',
                                'body' => 'El Umbral vibra. La Reina espera del otro lado con su propia verdad.',
                                'choices' => [
                                    ['text' => 'Cruzar con firmeza', 'outcome' => 'El juicio final comienza.', 'difficulty' => 2],
                                    ['text' => 'Entrar con un pacto preparado', 'outcome' => 'Intentas ganar tiempo antes del duelo.', 'difficulty' => 3],
                                    ['text' => 'Tomar el camino lateral', 'outcome' => 'Buscas una ventaja sutil.', 'difficulty' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'sal-del-destino',
                'title' => 'Sal del Destino',
                'intro_text' => 'El desierto blanco corta la piel y borra cualquier rastro. Sobrevivir es la primera prueba.',
                'context_text' => 'Debes cruzar las salinas antes de que el Titano de Sal despierte.',
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
                                    ['text' => 'Cruzar a campo abierto', 'outcome' => 'Avanzas rapido, pero el viento golpea fuerte.', 'difficulty' => 2, 'next' => 's2a'],
                                    ['text' => 'Seguir los restos de una caravana', 'outcome' => 'Las huellas te guian entre crestas.', 'difficulty' => 1, 'next' => 's2b'],
                                    ['text' => 'Tomar un desvio largo', 'outcome' => 'Pierdes tiempo, pero evitas lo peor.', 'difficulty' => 0, 'next' => 's2b'],
                                    ['text' => 'Atravesar un cañon estrecho', 'outcome' => 'La ruta es dura y te lastimas.', 'difficulty' => 3, 'next' => 's2a'],
                                ],
                            ],
                        ],
                        2 => [
                            [
                                'key' => 's2a',
                                'title' => 'Cañon de sal',
                                'body' => 'Las paredes reflejan la luz como espejos. El eco confunde la direccion.',
                                'choices' => [
                                    ['text' => 'Escalar por las vetas claras', 'outcome' => 'El ascenso agota, pero te orienta.', 'difficulty' => 2, 'next' => 's3a'],
                                    ['text' => 'Seguir el cauce seco', 'outcome' => 'La pendiente es amable y ahorras fuerzas.', 'difficulty' => 1, 'next' => 's3b'],
                                    ['text' => 'Forzar un atajo por grietas', 'outcome' => 'Te rasgas con cristales afilados.', 'difficulty' => 3, 'next' => 's3a'],
                                    ['text' => 'Esperar a que pase la ventisca', 'outcome' => 'Ganas claridad, pero pierdes horas.', 'difficulty' => 0, 'next' => 's3b'],
                                ],
                            ],
                            [
                                'key' => 's2b',
                                'title' => 'Laguna blanca',
                                'body' => 'La superficie se quiebra bajo tus botas. El agua salobre atrae peligros.',
                                'choices' => [
                                    ['text' => 'Bordear la laguna con cuidado', 'outcome' => 'Evitas hundirte en el barro salino.', 'difficulty' => 1, 'next' => 's3b'],
                                    ['text' => 'Cruzar en linea recta', 'outcome' => 'Avanzas rapido, pero resbalas y te golpeas.', 'difficulty' => 3, 'next' => 's3a'],
                                    ['text' => 'Buscar pasarelas de roca', 'outcome' => 'Encuentras un camino mas firme.', 'difficulty' => 2, 'next' => 's3b'],
                                    ['text' => 'Esperar la marea baja', 'outcome' => 'La ruta queda expuesta y sigues sin prisa.', 'difficulty' => 0, 'next' => 's3a'],
                                ],
                            ],
                        ],
                        3 => [
                            [
                                'key' => 's3a',
                                'title' => 'Caravana rota',
                                'body' => 'Carros destruidos y huellas frescas. Alguien paso hace poco.',
                                'choices' => [
                                    ['text' => 'Revisar los restos en busca de agua', 'outcome' => 'Encuentras un cantaro medio lleno.', 'difficulty' => 1, 'next' => 's4a'],
                                    ['text' => 'Perseguir las huellas sin pausa', 'outcome' => 'Aceleras, pero te fatigas y te hiere el sol.', 'difficulty' => 3, 'next' => 's4a'],
                                    ['text' => 'Usar los carros como cobertura', 'outcome' => 'Descansas antes de seguir.', 'difficulty' => 0, 'next' => 's4a'],
                                    ['text' => 'Desmontar ruedas para avanzar', 'outcome' => 'Improvisas un trineo de sal.', 'difficulty' => 2, 'next' => 's4a'],
                                ],
                            ],
                            [
                                'key' => 's3b',
                                'title' => 'Puesto abandonado',
                                'body' => 'Una torre de vigilancia vacia. Quedan señales de un ataque reciente.',
                                'choices' => [
                                    ['text' => 'Subir y observar el horizonte', 'outcome' => 'Ubicas rutas seguras antes de bajar.', 'difficulty' => 1, 'next' => 's4a'],
                                    ['text' => 'Revisar las reservas ocultas', 'outcome' => 'Encuentras provisiones, pero la estructura cede.', 'difficulty' => 3, 'next' => 's4a'],
                                    ['text' => 'Encender una senal corta', 'outcome' => 'Nadie responde, pero recuperas calma.', 'difficulty' => 0, 'next' => 's4a'],
                                    ['text' => 'Reforzar tu equipo con restos', 'outcome' => 'Las piezas ayudan a resistir el camino.', 'difficulty' => 2, 'next' => 's4a'],
                                ],
                            ],
                        ],
                        4 => [
                            [
                                'key' => 's4a',
                                'title' => 'Tormenta de cristales',
                                'body' => 'El viento se vuelve cuchilla. La sal golpea como granizo.',
                                'choices' => [
                                    ['text' => 'Cubrirte con telas y avanzar', 'outcome' => 'Resistes, pero terminas herido.', 'difficulty' => 3, 'next' => 's5a'],
                                    ['text' => 'Buscar refugio bajo una cornisa', 'outcome' => 'Esperas la tormenta con paciencia.', 'difficulty' => 0, 'next' => 's5b'],
                                    ['text' => 'Seguir el viento a favor', 'outcome' => 'Aprovechas la direccion para avanzar.', 'difficulty' => 2, 'next' => 's5a'],
                                    ['text' => 'Marcar un camino con estacas', 'outcome' => 'Aseguras tu ruta aunque gastas tiempo.', 'difficulty' => 1, 'next' => 's5b'],
                                ],
                            ],
                        ],
                        5 => [
                            [
                                'key' => 's5a',
                                'title' => 'Circulo de menhires',
                                'body' => 'Piedras antiguas forman un anillo. La vibracion del Titano crece.',
                                'choices' => [
                                    ['text' => 'Cruzar entre los menhires', 'outcome' => 'Sientes energia extraña pero avanzas.', 'difficulty' => 2, 'next' => 's6'],
                                    ['text' => 'Tomar un rodeo largo', 'outcome' => 'Evitas el riesgo directo.', 'difficulty' => 0, 'next' => 's6'],
                                    ['text' => 'Escalar la piedra central', 'outcome' => 'El esfuerzo te pasa factura.', 'difficulty' => 3, 'next' => 's6'],
                                ],
                            ],
                            [
                                'key' => 's5b',
                                'title' => 'Risco del silbido',
                                'body' => 'El viento suena como una advertencia. La arena se mueve sola.',
                                'choices' => [
                                    ['text' => 'Atravesar la ladera en diagonal', 'outcome' => 'La pendiente es dura pero estable.', 'difficulty' => 2, 'next' => 's6'],
                                    ['text' => 'Descender por el canal rocoso', 'outcome' => 'El canal te guía hacia el valle.', 'difficulty' => 1, 'next' => 's6'],
                                    ['text' => 'Cruzar por la arista expuesta', 'outcome' => 'El viento te golpea y te hiere.', 'difficulty' => 3, 'next' => 's6'],
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
                                    ['text' => 'Buscar un punto debil antes de entrar', 'outcome' => 'Preparas tu ataque con riesgo.', 'difficulty' => 3],
                                    ['text' => 'Rodear la plataforma con cautela', 'outcome' => 'Intentas ganar posicion.', 'difficulty' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
