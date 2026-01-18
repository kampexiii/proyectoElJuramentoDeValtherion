<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chest extends Model
{
    protected $fillable = [
        'name',
        'loot_table_id',
        'items_count',
    ];

    public function lootTable(): BelongsTo
    {
        return $this->belongsTo(LootTable::class);
    }

    public function openings(): HasMany
    {
        return $this->hasMany(ChestOpening::class);
    }
}
