<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rarity extends Model
{
    protected $fillable = [
        'name',
        'weight',
        'min_level',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }
}
