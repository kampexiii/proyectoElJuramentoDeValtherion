<?php

declare(strict_types=1);

namespace App\Services\Combat;

class CombatLogFormatter
{
    public function formatTurn(
        int $turnNumber,
        string $p1Action,
        string $p2Action,
        string $firstActor,
        int $damageToP1,
        int $damageToP2,
        int $p1Hp,
        int $p2Hp,
        bool $secondSkipped
    ): array {
        $lines = [];
        $lines[] = sprintf('Turno %d', $turnNumber);
        $lines[] = sprintf('P1 usa %s', $this->label($p1Action));
        $lines[] = sprintf('P2 usa %s', $this->label($p2Action));
        $lines[] = sprintf('Actua primero: %s', strtoupper($firstActor));
        $lines[] = sprintf('Danio a P1: %d', $damageToP1);
        $lines[] = sprintf('Danio a P2: %d', $damageToP2);
        $lines[] = sprintf('HP P1: %d | HP P2: %d', $p1Hp, $p2Hp);

        if ($secondSkipped) {
            $lines[] = 'El segundo ataque no se ejecuta por KO.';
        }

        return [
            'summary' => sprintf(
                'T%d: P1 %s, P2 %s, danios %d/%d',
                $turnNumber,
                $this->label($p1Action),
                $this->label($p2Action),
                $damageToP1,
                $damageToP2
            ),
            'lines' => $lines,
        ];
    }

    private function label(string $action): string
    {
        return match ($action) {
            'attack' => 'Atacar',
            'defend' => 'Defender',
            'magic' => 'Magia',
            default => strtoupper($action),
        };
    }
}
