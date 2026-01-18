<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Hero extends Model
{
    protected $fillable = [
        'race_id',
        'code',
        'name',
        'description',
        'base_hp',
        'base_strength',
        'base_magic',
        'base_defense',
        'base_speed',
        'unique_global',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function character(): HasOne
    {
        return $this->hasOne(Character::class);
    }
}
