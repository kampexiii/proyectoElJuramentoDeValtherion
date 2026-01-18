<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChestOpeningReward extends Model
{
    protected $fillable = [
        'chest_opening_id',
        'item_id',
        'quantity',
    ];

    public function chestOpening(): BelongsTo
    {
        return $this->belongsTo(ChestOpening::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
