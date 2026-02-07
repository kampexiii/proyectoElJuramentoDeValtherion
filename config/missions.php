<?php

declare(strict_types=1);

return [
    'difficulty' => [
        'tiers' => [
            'easy' => [
                'label' => 'Easy',
                'min' => 0,
                'max' => 5,
                'boss_multiplier' => 0.85,
                'race_points_multiplier' => 1.0,
            ],
            'normal' => [
                'label' => 'Normal',
                'min' => 6,
                'max' => 11,
                'boss_multiplier' => 1.00,
                'race_points_multiplier' => 1.1,
            ],
            'hard' => [
                'label' => 'Hard',
                'min' => 12,
                'max' => 15,
                'boss_multiplier' => 1.15,
                'race_points_multiplier' => 1.25,
            ],
            'brutal' => [
                'label' => 'Brutal',
                'min' => 16,
                'max' => 18,
                'boss_multiplier' => 1.30,
                'race_points_multiplier' => 1.45,
            ],
        ],
    ],
    'race_points_daily_cap' => 200,
];
