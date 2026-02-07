<?php

declare(strict_types=1);

namespace App\Services\Missions;

use App\Models\Mission;
use App\Models\MissionNode;

class MissionGraphValidator
{
    /**
     * @return string[]
     */
    public function validate(Mission $mission): array
    {
        $errors = [];

        if (!$mission->final_boss_id) {
            $errors[] = 'La mision debe tener un boss final asignado.';
        }

        if (!$mission->reward) {
            $errors[] = 'La mision debe tener rewards configurados.';
        }

        $nodes = $mission->nodes()->with('choices')->get();
        if ($nodes->isEmpty()) {
            $errors[] = 'La mision no tiene nodos.';
            return $errors;
        }

        $nodesById = $nodes->keyBy('id');
        $startNodes = $nodes->filter(fn (MissionNode $node) => (bool) $node->is_start);
        $startStepNodes = $startNodes->filter(fn (MissionNode $node) => (int) $node->step_index === 1);

        if ($startNodes->count() !== 1) {
            $errors[] = 'Debe existir exactamente 1 nodo marcado como inicio.';
        }

        if ($startStepNodes->count() !== 1) {
            $errors[] = 'El nodo de inicio debe pertenecer al paso 1.';
        }

        foreach (range(1, 6) as $step) {
            if ($nodes->where('step_index', $step)->isEmpty()) {
                $errors[] = 'Faltan nodos para el paso ' . $step . '.';
            }
        }

        $adjacency = [];

        foreach ($nodes as $node) {
            $choices = $node->choices;
            $choiceCount = $choices->count();

            if ($choiceCount < 3 || $choiceCount > 4) {
                $errors[] = 'El nodo #' . $node->id . ' debe tener 3 o 4 opciones.';
            }

            foreach ($choices as $choice) {
                $step = (int) $node->step_index;
                if ($step < 6) {
                    if ($choice->goes_to_boss) {
                        $errors[] = 'La opcion #' . $choice->id . ' del nodo #' . $node->id . ' no puede ir al boss (solo paso 6).';
                    }
                    if (!$choice->next_node_id) {
                        $errors[] = 'La opcion #' . $choice->id . ' del nodo #' . $node->id . ' debe tener siguiente nodo.';
                        continue;
                    }

                    $nextNode = $nodesById->get($choice->next_node_id);
                    if (!$nextNode || (int) $nextNode->step_index !== $step + 1) {
                        $errors[] = 'La opcion #' . $choice->id . ' del nodo #' . $node->id . ' debe apuntar al paso ' . ($step + 1) . '.';
                        continue;
                    }

                    $adjacency[$node->id][] = $nextNode->id;
                } else {
                    if (!$choice->goes_to_boss) {
                        $errors[] = 'La opcion #' . $choice->id . ' del nodo #' . $node->id . ' debe ir al boss final.';
                    }
                    if ($choice->next_node_id) {
                        $errors[] = 'La opcion #' . $choice->id . ' del nodo #' . $node->id . ' no puede tener siguiente nodo.';
                    }
                }
            }
        }

        if ($startStepNodes->count() === 1) {
            $startNode = $startStepNodes->first();
            $reachable = $this->collectReachable($startNode->id, $adjacency);
            $unreachable = $nodes->filter(fn (MissionNode $node) => !in_array($node->id, $reachable, true));

            if ($unreachable->isNotEmpty()) {
                $errors[] = 'Hay nodos inalcanzables desde el inicio: ' . $unreachable->pluck('id')->implode(', ') . '.';
            }
        }

        if ($this->hasCycle($nodes, $adjacency)) {
            $errors[] = 'El grafo de nodos contiene ciclos.';
        }

        return $errors;
    }

    /**
     * @param array<int, int[]> $adjacency
     * @return int[]
     */
    private function collectReachable(int $startId, array $adjacency): array
    {
        $visited = [];
        $queue = [$startId];

        while (!empty($queue)) {
            $current = array_shift($queue);
            if (in_array($current, $visited, true)) {
                continue;
            }

            $visited[] = $current;
            foreach ($adjacency[$current] ?? [] as $next) {
                if (!in_array($next, $visited, true)) {
                    $queue[] = $next;
                }
            }
        }

        return $visited;
    }

    /**
     * @param \Illuminate\Support\Collection<int, MissionNode> $nodes
     * @param array<int, int[]> $adjacency
     */
    private function hasCycle($nodes, array $adjacency): bool
    {
        $state = [];

        foreach ($nodes as $node) {
            $state[$node->id] = 'unvisited';
        }

        foreach (array_keys($state) as $nodeId) {
            if ($state[$nodeId] === 'unvisited' && $this->visitNode($nodeId, $state, $adjacency)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, string> $state
     * @param array<int, int[]> $adjacency
     */
    private function visitNode(int $nodeId, array &$state, array $adjacency): bool
    {
        $state[$nodeId] = 'visiting';

        foreach ($adjacency[$nodeId] ?? [] as $nextId) {
            if (!isset($state[$nextId])) {
                continue;
            }
            if ($state[$nextId] === 'visiting') {
                return true;
            }
            if ($state[$nextId] === 'unvisited' && $this->visitNode($nextId, $state, $adjacency)) {
                return true;
            }
        }

        $state[$nodeId] = 'visited';

        return false;
    }
}
