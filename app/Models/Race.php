<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    protected $fillable = [
        'name',
        'access',
        'lore',
        'base_hp',
        'base_strength',
        'base_magic',
        'base_defense',
        'base_speed',
    ];

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function heroes(): HasMany
    {
        return $this->hasMany(Hero::class);
    }
}
