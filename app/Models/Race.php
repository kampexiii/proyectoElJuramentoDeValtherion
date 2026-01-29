<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $fillable = [
        'name',
        'min_role',
        'stat_points_total',
        'base_hp',
        'base_strength',
        'base_magic',
        'base_defense',
        'base_speed',
        'lore',
        'caps_json',
        'bonuses_json',
    ];

    protected $casts = [
        'caps_json' => 'array',
        'bonuses_json' => 'array',
    ];
}
