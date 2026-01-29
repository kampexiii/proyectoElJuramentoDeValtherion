<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'type',
        'slot',
        'code',
        'required_level',
        'stackable',
        'sell_price',
        'value_gold',
        'is_consumable',
        'max_stack',
        'bonus_hp',
        'bonus_strength',
        'bonus_magic',
        'bonus_defense',
        'bonus_speed',
        'bonuses_json',
        'effects_json',
    ];

    protected $casts = [
        'stackable' => 'boolean',
        'is_consumable' => 'boolean',
        'bonuses_json' => 'array',
        'effects_json' => 'array',
    ];
}
