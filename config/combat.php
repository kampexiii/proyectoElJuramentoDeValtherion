<?php

declare(strict_types=1);

return [
    'defend_defense_multiplier' => 1.8,
    'boss_attack_every_turns' => 3,
    'wounds_hp_penalty_per_stack' => 0.02,
    'wounds_atk_penalty_per_stack' => 0.02,
    'potion' => [
        'heal_pct' => 0.30,
        'max_charges' => 2,
    ],
    'pve' => [
        'tiers' => [
            'easy' => [
                'max_hit_pct' => 0.35,
                'boss_hp_mult' => 0.80,
                'boss_atk_mult' => 0.75,
                'boss_def_mult' => 0.95,
            ],
            'normal' => [
                'max_hit_pct' => 0.45,
                'boss_hp_mult' => 0.95,
                'boss_atk_mult' => 0.90,
                'boss_def_mult' => 1.00,
            ],
            'hard' => [
                'max_hit_pct' => 0.55,
                'boss_hp_mult' => 1.10,
                'boss_atk_mult' => 1.05,
                'boss_def_mult' => 1.05,
            ],
            'brutal' => [
                'max_hit_pct' => 0.65,
                'boss_hp_mult' => 1.25,
                'boss_atk_mult' => 1.20,
                'boss_def_mult' => 1.10,
            ],
        ],
    ],
    'speed_tie_rng' => true,
    'damage' => [
        'attack_multiplier' => 1.0,
        'attack_defense_multiplier' => 0.6,
        'magic_multiplier' => 1.2,
        'magic_defense_multiplier' => 0.3,
    ],
];
