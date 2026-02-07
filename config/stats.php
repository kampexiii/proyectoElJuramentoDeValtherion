<?php

declare(strict_types=1);

return [
    'stats' => ['hp', 'attack', 'defense', 'speed', 'magic'],
    'base_stat_min' => 0,
    'base_stat_max' => 12,
    'multipliers' => [
        'hp' => ['base' => 1.35, 'growth' => 0.12],
        'attack' => ['base' => 1.00, 'growth' => 0.06],
        'defense' => ['base' => 1.00, 'growth' => 0.05],
        'speed' => ['base' => 1.00, 'growth' => 0.03],
        'magic' => ['base' => 1.00, 'growth' => 0.06],
    ],
    'rounding' => [
        'mode' => 'floor',
        'minimums' => [
            'hp' => 1,
            'attack' => 1,
            'defense' => 0,
            'speed' => 0,
            'magic' => 0,
        ],
    ],
];
