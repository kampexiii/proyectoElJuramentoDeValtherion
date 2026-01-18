<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name',
        'type',
        'rarity_id',
        'required_level',
        'stackable',
        'bonus_hp',
        'bonus_strength',
        'bonus_magic',
        'bonus_defense',
        'bonus_speed',
        'sell_price',
    ];

    protected $casts = [
        'stackable' => 'boolean',
    ];

    public function rarity(): BelongsTo
    {
        return $this->belongsTo(Rarity::class);
    }
}
