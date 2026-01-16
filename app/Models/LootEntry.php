<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LootEntry extends Model
{
    protected $fillable = [
        'loot_table_id',
        'entry_type',
        'item_id',
        'animal_id',
        'weight',
        'min_qty',
        'max_qty',
        'required_level',
    ];

    public function lootTable(): BelongsTo
    {
        return $this->belongsTo(LootTable::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
