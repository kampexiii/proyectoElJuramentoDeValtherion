<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootTable extends Model
{
    protected $fillable = [
        'name',
        'rolls',
        'min_level',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(LootEntry::class);
    }
}
